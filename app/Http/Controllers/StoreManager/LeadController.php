<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\LeadQuotation;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductShape;
use App\Models\ProductFeature;
use App\Models\InvoicePdfSetting;
use App\Support\LeadWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Index / Queue ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $roleSlug = optional(auth()->user()->crmRole)->slug;
              
         $query    = Lead::with(['customer', 'quotation'])
            ->withSum('payments as total_paid_amount', 'iPaidAmount')
            ->withSum('payments as total_discount_amount', 'iDiscountAmount')
            ->latest('iLeadId');


        if ($roleSlug !== 'storemanager') {
            $queue = LeadWorkflow::roleQueueStatuses($roleSlug);
            if (!empty($queue)) {
                $query->whereIn('iCurrentLeadStatus', $queue);
            }
            if ($roleSlug === 'fitting') {
                $query->where('isFittingRequired', 1);
                $query->where('iCurrentLeadStatus', '!=', LeadWorkflow::STATUS_RECEIVED_AT_NAROL);
            }
        }

        if ($roleSlug === 'account') {
            $query->where('iCurrentLeadStatus', '!=', LeadWorkflow::STATUS_LEAD_REJECTED);
        }


        if ($request->filled('status')) {
            $query->where('iCurrentLeadStatus', $request->status);
        }

        if ($request->filled('followup') && in_array($request->followup, ['today', 'overdue'], true)) {
            $today = now()->toDateString();
            /* if ($request->followup === 'today') {
                $query->whereDate('NetFollowupdate', $today);
            } elseif ($request->followup === 'overdue') {
                $query->whereDate('NetFollowupdate', '<', $today);
            }*/

            $operator = $request->followup === 'today' ? '=' : '<';

            $hasDispatchedDateColumn = Schema::hasColumn('leads', 'DispatchedDate');

            if ($roleSlug === 'fitting' && $hasDispatchedDateColumn) {
                $query->where(function ($q) use ($today, $operator) {
                    $q->whereDate('NetFollowupdate', $operator, $today)
                        ->orWhere(function ($sub) use ($today, $operator) {
                            $sub->whereNull('NetFollowupdate')
                                ->whereDate('DispatchedDate', $operator, $today);
                        });
                });
            } else {
                $query->whereDate('NetFollowupdate', $operator, $today);
            }


            /*$isTodayFilter = $request->followup === 'today';

            $query->where(function ($q) use ($today, $isTodayFilter, $roleSlug) {
                if ($isTodayFilter) {
                    $q->whereDate('NetFollowupdate', $today);
                } else {
                    $q->whereDate('NetFollowupdate', '<', $today);
                }

                if ($roleSlug === 'fitting') {
                    $q->orWhere(function ($fittingQuery) {
                        $fittingQuery->where('iCurrentLeadStatus', LeadWorkflow::STATUS_DISPATCHED)
                            ->whereNull('NetFollowupdate');
                    });
                }
            });*/
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('strLeadNo', 'like', "%{$search}%")
                    ->orWhere('iCurrentLeadStatus', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($sub) use ($search) {
                        $sub->where('strCustomer', 'like', "%{$search}%")
                            ->orWhere('strMobile', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('customer_type', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('CreatedDate', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('CreatedDate', '<=', $request->to_date);
        }


        $leads         = $query->paginate(15)->withQueryString();
        $statusOptions = LeadWorkflow::dashboardStatuses($roleSlug);

        return view('store-manager.leads.index', compact('leads', 'statusOptions', 'roleSlug'));
    }

    // ── Create ───────────────────────────────────────────────────────────────
    public function create()
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
        return view('store-manager.leads.create');
    }

    public function edit(Lead $lead)
    {
        abort_unless(LeadWorkflow::canEditLeadDetails($lead->iCurrentLeadStatus), 403, 'Lead cannot be edited after quotation approval.');
        //abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
        $lead->load('customer');

        return view('store-manager.leads.edit', compact('lead'));
    }

    // ── Check customer by mobile ─────────────────────────────────────────────
    public function checkCustomer(Request $request)
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
        $request->validate(['mobile' => 'required|digits:10']);
        $customer = Customer::where('strMobile', $request->mobile)->first();
        return response()->json($customer);
    }

    // ── Store new lead ───────────────────────────────────────────────────────
    public function store(Request $request)
    {

        //abort_unless(LeadWorkflow::canEditLeadDetails($lead->iCurrentLeadStatus), 403, 'Lead cannot be edited after quotation approval.');
        //abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

        $data = $request->validate([
            'strMobile'             => 'required|digits:10',
            'strCustomer'           => 'required|string|max:100',
            'strAddress'            => 'nullable|string',
            'SiteAddress'           => 'nullable|string',
            'customer_type'         => 'required|in:B2B,Retail',
            'company_name'          => 'nullable|string|max:150',
            'IsOnlyFittingQuotation' => 'required|in:0,1',
            'isFittingChargeIncluded' => [
                'nullable',
                'in:0,1',
                Rule::requiredIf(fn() => (int) $request->input('IsOnlyFittingQuotation') === 1),
            ],

            'IsMeasureMentRequired' => 'required|in:0,1',
            'MeasurementVisitDate'  => 'nullable|date|required_if:IsMeasureMentRequired,1',
            'design_followup_date'  => 'nullable|date|required_if:IsMeasureMentRequired,0',
            'strComments'           => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::firstOrCreate(
                ['strMobile' => $data['strMobile']],
                [
                    'strCustomer' => $data['strCustomer'],
                    'strAddress' => $data['strAddress'] ?? null,
                    'customer_type' => $data['customer_type'],
                    'company_name' => $data['company_name'] ?? null,
                ]
            );

            if (!$customer->wasRecentlyCreated) {
                $customer->update([
                    'strCustomer' => $data['strCustomer'],
                    'strAddress'  => $data['strAddress'] ?? null,
                    'customer_type' => $data['customer_type'],
                    'company_name' => $data['company_name'] ?? null,
                ]);
            }

            if ($customer->wasRecentlyCreated) {
                $customer->update([
                    'customer_type' => $data['customer_type'],
                    'company_name' => $data['company_name'] ?? null,
                ]);
            }

            $fy        = now()->format('y') . now()->addYear()->format('y');
            $leadCount = Lead::where('iCurrentYearLeadId', $fy)->count() + 1;
            $leadNo    = $fy . '-' . str_pad($leadCount, 4, '0', STR_PAD_LEFT);

            $isMeasurementRequired = (int) $data['IsMeasureMentRequired'] === 1;
            $isOnlyFittingQuotation = (int) $data['IsOnlyFittingQuotation'] === 1;
            $isFittingRequired = $isOnlyFittingQuotation ? 1 : 0;
            $isFittingChargeIncluded = $isFittingRequired ? (int) ($data['isFittingChargeIncluded'] ?? 0) : 0;
            $status = $isMeasurementRequired
                ? LeadWorkflow::STATUS_IN_MEASUREMENT
                : LeadWorkflow::STATUS_IN_DESIGN;

            $nextFollowDate = $isMeasurementRequired
                ? ($data['MeasurementVisitDate'] ?? null)
                : ($data['design_followup_date'] ?? null);

            $lead = Lead::create([
                'iCustomerId'           => $customer->iCustomerId,
                'iCurrentYearLeadId'    => $fy,
                'strLeadNo'             => $leadNo,
                'IsMeasureMentRequired' => $data['IsMeasureMentRequired'],
                'MeasurementVisitDate'  => $data['MeasurementVisitDate'] ?? null,
                'SiteAddress'           => $data['SiteAddress'] ?? null,
                'CreatedDate'           => now(),
                'iCurrentLeadStatus'    => $status,
                'NetFollowupdate'       => $nextFollowDate,
                'isFittingLeadOnly'     => $isOnlyFittingQuotation ? 1 : 0,
                'isFittingRequired'     => $isFittingRequired,
                'isFittingChargeIncluded' => $isFittingChargeIncluded,
                'iCreatedBy'            => auth()->id(),
            ]);

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
                'strComments'     => $data['strComments'] ?? 'Lead created.',
                'NetFolloupwdate' => $nextFollowDate,
                'iStatus'         => $status,
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);

            DB::commit();
            return redirect()->route('store.leads.index')
                //  return redirect()->route('store.leads.histories.index', $lead->iLeadId)
                ->with('success', 'Lead created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }
    public function update(Request $request, Lead $lead)
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

        $data = $request->validate([
            'strCustomer'         => 'required|string|max:100',
            'strAddress'          => 'nullable|string',
            'SiteAddress'         => 'nullable|string',
            'customer_type'       => 'required|in:B2B,Retail',
            'company_name'        => 'nullable|string|max:150',
            'IsOnlyFittingQuotation' => 'required|in:0,1',
            'isFittingChargeIncluded' => [
                'nullable',
                'in:0,1',
            Rule::requiredIf(fn() => (int) $request->input('IsOnlyFittingQuotation') === 1),
            ],
            'IsMeasureMentRequired' => 'required|in:0,1',
            'MeasurementVisitDate'  => 'nullable|date|required_if:IsMeasureMentRequired,1',
            'design_followup_date'  => 'nullable|date|required_if:IsMeasureMentRequired,0',
            'strComments'           => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $customer = $lead->customer;
            if ($customer) {
                $customer->update([
                    'strCustomer'   => $data['strCustomer'],
                    'strAddress'    => $data['strAddress'] ?? null,
                    'customer_type' => $data['customer_type'],
                    'company_name'  => $data['company_name'] ?? null,
                ]);
            }

            $isMeasurementRequired = (int) $data['IsMeasureMentRequired'] === 1;
            $isOnlyFittingQuotation = (int) $data['IsOnlyFittingQuotation'] === 1;
            $isFittingRequired = $isOnlyFittingQuotation ? 1 : 0;
            $isFittingChargeIncluded = $isFittingRequired ? (int) ($data['isFittingChargeIncluded'] ?? 0) : 0;
            $nextFollowDate = $isMeasurementRequired
                ? ($data['MeasurementVisitDate'] ?? null)
                : ($data['design_followup_date'] ?? null);

            $lead->update([
                'IsMeasureMentRequired'   => $data['IsMeasureMentRequired'],
                'MeasurementVisitDate'    => $data['MeasurementVisitDate'] ?? null,
                'SiteAddress'             => $data['SiteAddress'] ?? null,
                'NetFollowupdate'         => $nextFollowDate,
                'isFittingLeadOnly'       => $isOnlyFittingQuotation ? 1 : 0,
                'isFittingRequired'       => $isFittingRequired,
                'isFittingChargeIncluded' => $isFittingChargeIncluded,
            ]);

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
                'strComments'     => $data['strComments'] ?? 'Lead details updated.',
                'NetFolloupwdate' => $nextFollowDate,
                'iStatus'         => $lead->iCurrentLeadStatus,
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);

            DB::commit();

            return redirect()->route('store.leads.index')
                ->with('success', 'Lead updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }


    // ── Quotation form ───────────────────────────────────────────────────────
    public function quotationForm(Lead $lead)
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

        $categories = ProductCategory::orderBy('strCategoryName', 'asc')->get();
        $products   = Product::orderBy('strProductName', 'asc')->get();
        $shapes     = ProductShape::where('iStatus', 1)->where('isDelete', 0)->orderBy('shape_title', 'asc')->get();
        $features   = ProductFeature::where('iStatus', 1)->where('isDelete', 0)->orderBy('feature_name', 'asc')->get();

        /*$lead->load('quotations');

        return view('store-manager.leads.quotation', compact('lead', 'categories', 'products', 'shapes', 'features'));*/

        $activeQuotation = $lead->quotation;
        $activeBatchId = $activeQuotation?->quotation_batch_id;

        $activeQuotations = $activeBatchId
            ? $lead->quotations()->where('quotation_batch_id', $activeBatchId)->get()
            : collect();

        $quotationVersions = $lead->quotations()
            ->selectRaw('quotation_batch_id, COUNT(*) as line_items, SUM(iAmount) as amount, MAX(created_at) as created_at')
            ->groupBy('quotation_batch_id')
            ->orderByDesc('quotation_batch_id')
            ->get();

        $quotationHistoryBatches = $lead->quotations()
            ->with(['category', 'product', 'shape', 'feature'])
            ->orderByDesc('quotation_batch_id')
            ->orderBy('iQuotationId')
            ->get()
            ->groupBy('quotation_batch_id')
            ->map(function ($items, $batchId) {
                return (object) [
                    'quotation_batch_id' => (int) $batchId,
                    'items'              => $items->values(),
                    'line_items'         => $items->count(),
                    'subtotal'           => (float) $items->sum('iAmount'),
                    'created_at'         => optional($items->max('created_at')),
                ];
            })
            ->sortByDesc('quotation_batch_id')
            ->values();


        return view('store-manager.leads.quotation', compact(
            'lead',
            'categories',
            'products',
            'shapes',
            'features',
            'activeQuotations',
            'quotationVersions',
            'quotationHistoryBatches'

        ));
    }

    // ── Save quotation ───────────────────────────────────────────────────────
    public function saveQuotation(Request $request, Lead $lead)
    {

        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
        if ($request->iFittingCharges > 0) {
            $requiresManualFittingCharge = 1;
        } else {
            $requiresManualFittingCharge = 0;
        }

        $rules = [
            //'iProductCategoryId'                      => 'required|exists:product_categories,iCategoryId',
            'items'                                    => 'required|array|min:1',
            'items.*.iProductCategoryId'               => 'required|exists:product_categories,iCategoryId',
            'items.*.iProductId'                       => 'required|exists:products,iProductId',
            'items.*.unit_of_measurement'              => 'required|in:inch,MM,Feet',
            'items.*.shape_id'                         => 'required|exists:product_shape,shape_id',
            'items.*.feature_id'                       => 'required|exists:product_feature,feature_id',
            'items.*.remarks'                          => 'nullable|string|max:255',
            'items.*.quantity'                         => 'required|integer|min:1',
            'items.*.decHeight'                        => 'required|numeric|min:0',
            'items.*.decWidth'                         => 'required|numeric|min:0',
            'items.*.decRatePerSqft'                   => 'required|numeric|min:0',
            'items.*.calculation_multiple'             => 'required|integer|in:3,6',
            'followup_date'                            => 'required|date',
            'isFittingRequired'                        => 'required|in:0,1',
            'iFittingCharges'                          => $requiresManualFittingCharge
                ? 'required|numeric|min:0'
                : 'nullable|numeric|min:0',
            'isDiscountApplicable'                     => 'required|in:0,1',
            'discount_amount'                          => 'nullable|numeric|min:0',
            'isGstApplicable'                          => 'required|in:0,1',
            'strComments'                              => 'nullable|string|max:2000',
            'delivery_charges'                          => $requiresManualFittingCharge
                ? 'required|numeric|min:0'
                : 'nullable|numeric|min:0',
        ];

        $data = $request->validate($rules);

        $productCategoryMap = Product::query()
            ->whereIn('iProductId', collect($data['items'])->pluck('iProductId')->all())
            ->pluck('iCategoryId', 'iProductId');

        foreach ($data['items'] as $index => $item) {
            $selectedProductCategory = (int) ($productCategoryMap[$item['iProductId']] ?? 0);
            if ($selectedProductCategory !== (int) $item['iProductCategoryId']) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.iProductId" => 'Selected product does not belong to selected category for this row.',
                    ]);
            }
        }


        DB::beginTransaction();

        try {
            $subtotal       = 0;
            $firstQuotation = null;

            //LeadQuotation::where('iLeadId', $lead->iLeadId)->delete();
            $nextBatchId = ((int) LeadQuotation::where('iLeadId', $lead->iLeadId)->max('quotation_batch_id')) + 1;

            foreach ($data['items'] as $item) {

                $qty      = (int) $item['quantity'];
                $height   = (float) $item['decHeight'];
                $width    = (float) $item['decWidth'];
                $rate     = (float) $item['decRatePerSqft'];
                $unit     = $item['unit_of_measurement'];
                $multiple = (int) $item['calculation_multiple'];

                $heightFeet = $this->convertToFeet($height, $unit, $multiple);
                $widthFeet  = $this->convertToFeet($width, $unit, $multiple);

                $sqft   = $qty * $widthFeet * $heightFeet;
                $amount = $sqft * $rate;

                $quotation = LeadQuotation::create([
                    'iLeadId'             => $lead->iLeadId,
                    'quotation_batch_id'  => $nextBatchId,
                    'iProductCategoryId'  => $item['iProductCategoryId'],
                    'iProductId'          => $item['iProductId'],
                    'unit_of_measurement' => $unit,
                    'shape_id'            => $item['shape_id'],
                    'feature_id'          => $item['feature_id'],
                    'remarks'             => $item['remarks'] ?? null,
                    'quantity'            => $qty,
                    'decHeight'           => $height,
                    'decWidth'            => $width,
                    'decRatePerSqft'      => $rate,
                    'decTotalSqft'        => $sqft,
                    'iAmount'             => $amount,
                ]);

                if (!$firstQuotation) {
                    $firstQuotation = $quotation;
                }

                $subtotal += $amount;
            }

            $fittingCharges = (float) ($data['iFittingCharges'] ?? 0);
            $deliveryCharges = (float) ($data['delivery_charges'] ?? 0);
            $baseAmount = $subtotal + $fittingCharges + $deliveryCharges;

            $discountApplicable = (int) $data['isDiscountApplicable'] === 1;
            $rawDiscount = (float) ($data['discount_amount'] ?? 0);
            $discount = $discountApplicable ? min($rawDiscount, $baseAmount) : 0;

            $afterDiscount = $baseAmount - $discount;

            // ✅ GST
            $gstApplicable = (int) $data['isGstApplicable'] === 1;
            $gstAmount = $gstApplicable ? ($afterDiscount * 0.18) : 0;

            $grandTotal = $afterDiscount + $gstAmount;

            $lead->update([
                'iQuotationId'          => $firstQuotation?->iQuotationId,
                'iLeadAmount'           => $grandTotal,
                'NetFollowupdate'       => $data['followup_date'],
                'isFittingRequired'     => (int) $data['isFittingRequired'],
                'iFittingCharges'       => $fittingCharges,
                'isDiscountApplicable'  => $discountApplicable ? 1 : 0,
                'decDiscountAmount'     => $discount,
                'isGstApplicable'       => $gstApplicable ? 1 : 0,
                'decGstAmount'          => $gstAmount,
                'delivery_charges'      => $deliveryCharges ?? 0,
            ]);

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
                'strComments'     => $data['strComments'] ?? ('Quotation generated. Version #' . $nextBatchId),
                'NetFolloupwdate' => $data['followup_date'],
                'iStatus'         => $lead->iCurrentLeadStatus,
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);
            DB::commit();
            return redirect()->route('store.leads.index')->with('success', 'Quotation generated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }
    private function convertToFeet($value, $unit, $multiple)
    {
        if (!$value || $value <= 0) {
            return 0;
        }
        if ($unit === 'MM') {
            $inch = $value / 25.4;
        } elseif ($unit === 'inch') {
            $inch = $value;
        } elseif ($unit === 'Feet') {
            return $value;
        } else {
            $inch = $value;
        }
        $adjustedInch = ceil($inch / $multiple) * $multiple;
        return $adjustedInch / 12;
    }

    // public function saveQuotation(Request $request, Lead $lead)
    // {
    //     abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

    //     $requiresManualFittingCharge = (int) $lead->isFittingRequired === 1 && (int) $lead->isFittingChargeIncluded === 0;

    //     $rules = [
    //         //'iProductCategoryId'                      => 'required|exists:product_categories,iCategoryId',
    //         'items'                                    => 'required|array|min:1',
    //         'items.*.iProductCategoryId'               => 'required|exists:product_categories,iCategoryId',
    //         'items.*.iProductId'                       => 'required|exists:products,iProductId',
    //         'items.*.unit_of_measurement'              => 'required|in:inch,MM,Feet',
    //         'items.*.shape_id'                         => 'required|exists:product_shape,shape_id',
    //         'items.*.feature_id'                       => 'required|exists:product_feature,feature_id',
    //         'items.*.remarks'                          => 'nullable|string|max:255',
    //         'items.*.quantity'                         => 'required|integer|min:1',
    //         'items.*.decHeight'                        => 'required|numeric|min:0',
    //         'items.*.decWidth'                         => 'required|numeric|min:0',
    //         'items.*.decRatePerSqft'                   => 'required|numeric|min:0',
    //         'items.*.calculation_multiple'             => 'required|integer|in:3,6',
    //         'followup_date'                            => 'required|date',
    //         'iFittingCharges'                          => $requiresManualFittingCharge
    //                                                         ? 'required|numeric|min:0'
    //                                                         : 'nullable|numeric|min:0',
    //         'isDiscountApplicable'                     => 'required|in:0,1',
    //         'discount_amount'                          => 'nullable|numeric|min:0',
    //         'isGstApplicable'                          => 'required|in:0,1',
    //         'strComments'                              => 'nullable|string|max:2000',                                               
    //     ];

    //     $data = $request->validate($rules);

    //     $productCategoryMap = Product::query()
    //         ->whereIn('iProductId', collect($data['items'])->pluck('iProductId')->all())
    //         ->pluck('iCategoryId', 'iProductId');

    //     foreach ($data['items'] as $index => $item) {
    //         $selectedProductCategory = (int) ($productCategoryMap[$item['iProductId']] ?? 0);
    //         if ($selectedProductCategory !== (int) $item['iProductCategoryId']) {
    //             return back()
    //                 ->withInput()
    //                 ->withErrors([
    //                     "items.$index.iProductId" => 'Selected product does not belong to selected category for this row.',
    //                 ]);
    //         }
    //     }


    //     DB::beginTransaction();

    //     try {
    //         $subtotal       = 0;
    //         $firstQuotation = null;

    //         //LeadQuotation::where('iLeadId', $lead->iLeadId)->delete();
    //         $nextBatchId = ((int) LeadQuotation::where('iLeadId', $lead->iLeadId)->max('quotation_batch_id')) + 1;

    //         foreach ($data['items'] as $item) {
    //             $qty    = (int) $item['quantity'];
    //             /*$sqft   = (float) $item['decHeight'] * (float) $item['decWidth'];
    //             $amount = $qty * $sqft * (float) $item['decRatePerSqft'];*/

    //             $height = (float) $item['decHeight'];
    //             $width  = (float) $item['decWidth'];
    //             $calculationMultiple = (int) ($item['calculation_multiple'] ?? 3);

    //             $actualSqft = $height * $width;
    //             $calculationHeight = $this->normalizeDimensionForAmount($height, $calculationMultiple);
    //             $calculationWidth  = $this->normalizeDimensionForAmount($width, $calculationMultiple);
    //             $calculationSqft   = $calculationHeight * $calculationWidth;

    //             $amount = $qty * $calculationSqft * (float) $item['decRatePerSqft'];


    //             $quotation = LeadQuotation::create([
    //                 'iLeadId'             => $lead->iLeadId,
    //                 'quotation_batch_id'  => $nextBatchId,
    //                 'iProductCategoryId'  => $item['iProductCategoryId'],
    //                 'iProductId'          => $item['iProductId'],
    //                 'unit_of_measurement' => $item['unit_of_measurement'],
    //                 'shape_id'            => $item['shape_id'],
    //                 'feature_id'          => $item['feature_id'],
    //                 'remarks'             => $item['remarks'] ?? null,
    //                 'quantity'            => $qty,
    //                 /*'decHeight'           => $item['decHeight'],
    //                 'decWidth'            => $item['decWidth'],*/
    //                 'decHeight'           => $height,
    //                 'decWidth'            => $width,
    //                 'decRatePerSqft'      => $item['decRatePerSqft'],
    //                 'decTotalSqft'        => $actualSqft,
    //                 'iAmount'             => $amount,
    //             ]);

    //             if (!$firstQuotation) {
    //                 $firstQuotation = $quotation;
    //             }

    //             $subtotal += $amount;
    //         }

    //         $fittingCharges = $requiresManualFittingCharge
    //             ? (float) ($data['iFittingCharges'] ?? 0)
    //             : 0;

    //         //$grandTotal = $subtotal + $fittingCharges;

    //         $discountApplicable = (int) ($data['isDiscountApplicable'] ?? 0) === 1;
    //         $rawDiscountAmount = (float) ($data['discount_amount'] ?? 0);
    //         $baseAmount = $subtotal + $fittingCharges;
    //         $discountAmount = $discountApplicable ? min($rawDiscountAmount, $baseAmount) : 0;
    //         $amountAfterDiscount = $baseAmount - $discountAmount;

    //         $gstApplicable = (int) ($data['isGstApplicable'] ?? 0) === 1;
    //         $gstAmount = $gstApplicable ? ($amountAfterDiscount * 0.18) : 0;

    //         $grandTotal = $amountAfterDiscount + $gstAmount;


    //         $lead->update([
    //             /*'iQuotationId'       => $firstQuotation ? $firstQuotation->iQuotationId : null,
    //             'iLeadAmount'        => $grandTotal,
    //             'iCurrentLeadStatus' => LeadWorkflow::STATUS_QUOTATION_SENT,
    //             'NetFollowupdate'    => $data['followup_date'],
    //             'iFittingCharges'    => $fittingCharges,
    //       */
    //             'iQuotationId'          => $firstQuotation ? $firstQuotation->iQuotationId : null,
    //             'iLeadAmount'           => $grandTotal,
    //             //'iCurrentLeadStatus'    => LeadWorkflow::STATUS_QUOTATION_SENT,
    //             'NetFollowupdate'       => $data['followup_date'],
    //             'iFittingCharges'       => $fittingCharges,
    //             'isDiscountApplicable'  => $discountApplicable ? 1 : 0,
    //             'decDiscountAmount'     => $discountAmount,
    //             'isGstApplicable'       => $gstApplicable ? 1 : 0,
    //             'decGstAmount'          => $gstAmount,
    //         ]);

    //         LeadHistory::create([
    //             'iLeadId'         => $lead->iLeadId,
    //             'strComments'     => $data['strComments'] ?? ('Quotation generated. Version #' . $nextBatchId),
    //             'NetFolloupwdate' => $data['followup_date'],
    //             //'iStatus'         => LeadWorkflow::STATUS_QUOTATION_SENT,
    //             'iStatus'         => $lead->iCurrentLeadStatus,
    //             'iEnterBy'        => auth()->id(),
    //             'EntryDate'       => now(),
    //         ]);

    //         DB::commit();

    //         //return redirect()->route('store.leads.histories.index', $lead->iLeadId)

    //         return redirect()->route('store.leads.index')->with('success', 'Quotation generated successfully.');
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return back()->withInput()->with('error', $th->getMessage());
    //     }
    // }

    // ── Quotation view (HTML) ────────────────────────────────────────────────
    public function quotationView(Lead $lead)
    {
        $isAdminLike = blank(optional(auth()->user()->crmRole)->slug);
        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
                optional(auth()->user()->crmRole)->slug === 'storemanager' ||
                $isAdminLike,
            403
        );

        $lead->load(['customer', 'quotation']);

        if (!$lead->quotation) {
            return redirect()->route('store.leads.index')->with('error', 'Quotation not found for this lead.');
        }


        $activeBatchId = $lead->quotation->quotation_batch_id;
        $activeItems = $lead->quotations()
            ->where('quotation_batch_id', $activeBatchId)
            ->with(['category', 'product', 'shape', 'feature'])
            ->get();
        $lead->setRelation('quotations', $activeItems);

        $canViewFinancial = (bool) auth()->user()->can_view_financial;

        $roleSlug = optional(auth()->user()->crmRole)->slug;
        $canViewFittingCharges = $roleSlug === 'fitting';
        $dispatch = $roleSlug === 'dispatch';

        return view('store-manager.leads.quotation-view', compact('lead', 'canViewFinancial', 'canViewFittingCharges', 'dispatch'));
    }

    // ── Quotation PDF ────────────────────────────────────────────────────────
    public function quotationPdf(Lead $lead)
    {

        $isAdminLike = blank(optional(auth()->user()->crmRole)->slug);
        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
                optional(auth()->user()->crmRole)->slug === 'storemanager' ||
                $isAdminLike,
            403
        );

        $lead->load(['customer', 'quotation', 'createdBy']);


        if (!$lead->quotation) {
            return redirect()->back()->with('error', 'Quotation not found for this lead.');
        }

        $activeBatchId = $lead->quotation->quotation_batch_id;
        $activeItems = $lead->quotations()
            ->where('quotation_batch_id', $activeBatchId)
            ->with(['category', 'product', 'shape', 'feature'])
            ->get();
        $lead->setRelation('quotations', $activeItems);

        $canViewFinancial = (bool) auth()->user()->can_view_financial;

        //$pdf = Pdf::loadView('store-manager.leads.quotation-pdf-document', compact('lead', 'canViewFinancial'));
        $invoicePdfSetting = InvoicePdfSetting::query()->first();
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        $canViewFittingCharges = $roleSlug === 'fitting';
        $dispatch = $roleSlug === 'dispatch';

            $pdfView = $roleSlug === 'account'
            ? 'store-manager.leads.invoice-pdf-document'
            : 'store-manager.leads.quotation-pdf-document';

        /*$pdf = Pdf::loadView('store-manager.leads.quotation-pdf-document', compact('lead', 'canViewFinancial', 'invoicePdfSetting', 'canViewFittingCharges', 'dispatch'));*/
        $pdf = Pdf::loadView($pdfView, compact('lead', 'canViewFinancial', 'invoicePdfSetting', 'canViewFittingCharges', 'dispatch'));
        $pdf->setPaper('a4', 'portrait');
        $pdfPrefix = $roleSlug === 'account' ? 'invoice-' : 'quotation-';
        return $pdf->stream($pdfPrefix . $lead->strLeadNo . '.pdf');
        /*
        return $pdf->stream('quotation-' . $lead->strLeadNo . '.pdf');*/

        //   return view('store-manager.leads.quotation-pdf', compact('lead', 'canViewFinancial'));
    }

    // ── Update status (legacy — kept for compatibility) ─────────────────────
    public function updateStatus(Request $request, Lead $lead)
    {
        abort_unless(LeadWorkflow::canAccessLead(auth()->user(), $lead), 403);

        $data = $request->validate([
            'iStatus'         => 'required|string|in:' . implode(',', LeadWorkflow::allStatuses()),
            'NetFollowupdate' => 'nullable|date',
            'strComments'     => 'required|string',
        ]);

        $roleSlug      = optional(auth()->user()->crmRole)->slug;
        $followupNeeded = in_array($data['iStatus'], LeadWorkflow::followupRequiredStatuses(), true);

        if ($followupNeeded && empty($data['NetFollowupdate'])) {
            return back()->withInput()->withErrors([
                'NetFollowupdate' => 'Next follow up date is required for the selected status.'
            ]);
        }

        if (
            $roleSlug !== 'storemanager' &&
            !in_array($data['iStatus'], LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead), true)
        ) {
            return back()->withInput()->with('error', 'You cannot move this lead to the selected status.');
        }

        DB::beginTransaction();
        try {
            $lead->update([
                'iCurrentLeadStatus' => $data['iStatus'],
                'NetFollowupdate'    => $data['NetFollowupdate'] ?? null,
            ]);

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
                'strComments'     => $data['strComments'],
                'NetFolloupwdate' => $data['NetFollowupdate'] ?? null,
                'iStatus'         => $data['iStatus'],
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);

            DB::commit();

            return redirect()->route('store.leads.index')->with('success', 'Quotation generated successfully.');
            //return redirect()->route('store.leads.histories.index', $lead->iLeadId)->with('success', 'Lead status updated to "' . $data['iStatus'] . '".');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
    private function normalizeDimensionForAmount(float $value, int $multiple): float
    {
        if ($value <= 0) {
            return 0;
        }

        $normalizedMultiple = $multiple === 6 ? 6 : 3;

        return (float) (ceil($value / $normalizedMultiple) * $normalizedMultiple);
    }
    public function deliveryChallan(Lead $lead)
    {
        // Accessible by dispatch role and storemanager
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        abort_unless(
            in_array($roleSlug, ['dispatch', 'storemanager']),
            403,
            'Delivery Challan is only accessible to dispatch users.'
        );

        if (!$lead->quotation) {
            return redirect()->back()->with('error', 'No quotation found for this lead.');
        }

        $lead->load(['customer', 'quotation']);

        $activeBatchId   = $lead->quotation->quotation_batch_id;
        $quotationItems  = $lead->quotations()
            ->where('quotation_batch_id', $activeBatchId)
            ->with(['category', 'product', 'shape', 'feature'])
            //->with(['product', 'shape'])
            ->get();

        //        return view('store-manager.leads.delivery-challan', compact('lead', 'quotationItems'));
        $pdf = Pdf::loadView('store-manager.leads.delivery-challan', compact('lead', 'quotationItems'));

        return $pdf->stream('delivery-challan-' . $lead->strLeadNo . '.pdf');
    }
}