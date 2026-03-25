<?php
 
 namespace App\Http\Controllers\StoreManager;
 
 use App\Http\Controllers\Controller;
 use App\Models\Customer;
 use App\Models\Lead;
 use App\Models\LeadHistory;
 use App\Models\LeadQuotation;
 use App\Models\Product;
 use App\Models\ProductCategory;
 use Carbon\Carbon;
 use App\Support\LeadWorkflow;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;
 
 class LeadController extends Controller
 {
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index(Request $request)
     {
        $roleSlug = optional(auth()->user()->crmRole)->slug;
         $query = Lead::with(['customer', 'quotation'])->latest('iLeadId');
 
        if ($roleSlug !== 'storemanager') {
            $query->whereIn('iCurrentLeadStatus', LeadWorkflow::roleQueueStatuses($roleSlug));
        }

        if ($request->filled('status')) {
            $query->where('iCurrentLeadStatus', $request->status);
        }

        if ($request->filled('followup') && in_array($request->followup, ['today', 'overdue'], true)) {
            $today = now()->toDateString();
            if ($request->followup === 'today') {
                $query->whereDate('NetFollowupdate', $today);
            }

            if ($request->followup === 'overdue') {
                $query->whereDate('NetFollowupdate', '<', $today);
            }
        }

         if ($request->filled('search')) {
             $search = trim($request->search);
 
             $query->where(function ($q) use ($search) {
                 $q->where('strLeadNo', 'like', "%{$search}%")
                     ->orWhere('iCurrentLeadStatus', 'like', "%{$search}%")
                     ->orWhereHas('customer', function ($sub) use ($search) {
                         $sub->where('strCustomer', 'like', "%{$search}%")
                             ->orWhere('strMobile', 'like', "%{$search}%");
                     });
             });
         }
 
         $leads = $query->paginate(15)->withQueryString();
        $statusOptions = LeadWorkflow::dashboardStatuses($roleSlug);
 
        return view('store-manager.leads.index', compact('leads', 'statusOptions', 'roleSlug'));
     }
 
     public function create()
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         return view('store-manager.leads.create');
     }
 
     public function checkCustomer(Request $request)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $request->validate([
             'mobile' => 'required|digits:10',
         ]);
 
         $customer = Customer::where('strMobile', $request->mobile)->first();
 
         return response()->json($customer);
     }
 
     public function store(Request $request)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $data = $request->validate([
             'strMobile' => 'required|digits:10',
             'strCustomer' => 'required|string|max:100',
             'strAddress' => 'nullable|string',
             'SiteAddress' => 'nullable|string',
             'IsMeasureMentRequired' => 'required|in:0,1',
             'MeasurementVisitDate' => 'nullable|date|required_if:IsMeasureMentRequired,1',
            'design_followup_date' => 'nullable|date|required_if:IsMeasureMentRequired,0',
         ]);
 
         DB::beginTransaction();
 
         try {
             $customer = Customer::firstOrCreate(
                 ['strMobile' => $data['strMobile']],
                 [
                     'strCustomer' => $data['strCustomer'],
                     'strAddress' => $data['strAddress'] ?? null,
                 ]
             );
 
            if (! $customer->wasRecentlyCreated) {
                 $customer->update([
                     'strCustomer' => $data['strCustomer'],
                     'strAddress' => $data['strAddress'] ?? null,
                 ]);
             }
 
             $fy = now()->format('y') . now()->addYear()->format('y');
             $leadCount = Lead::where('iCurrentYearLeadId', $fy)->count() + 1;
             $leadNo = $fy . '-' . str_pad($leadCount, 4, '0', STR_PAD_LEFT);
             $isMeasurementRequired = (int) $data['IsMeasureMentRequired'] === 1;

            $status = $isMeasurementRequired ? LeadWorkflow::STATUS_IN_MEASUREMENT : LeadWorkflow::STATUS_IN_DESIGN;
             $nextFollowDate = $isMeasurementRequired
                 ? ($data['MeasurementVisitDate'] ?? null)
                : ($data['design_followup_date'] ?? null);
 
            $lead = Lead::create([
                 'iCustomerId' => $customer->iCustomerId,
                 'iCurrentYearLeadId' => $fy,
                 'strLeadNo' => $leadNo,
                 'IsMeasureMentRequired' => $data['IsMeasureMentRequired'],
                 'MeasurementVisitDate' => $data['MeasurementVisitDate'] ?? null,
                 'SiteAddress' => $data['SiteAddress'] ?? null,
                 'CreatedDate' => now(),
                 'iCurrentLeadStatus' => $status,
                 'NetFollowupdate' => $nextFollowDate,
                 'iCreatedBy' => auth()->id(),
             ]);
 
            LeadHistory::create([
                'iLeadId' => $lead->iLeadId,
                'strComments' => 'Lead created.',
                'NetFolloupwdate' => $nextFollowDate,
                'iStatus' => $status,
                'iEnterBy' => auth()->id(),
                'EntryDate' => now(),
            ]);

             DB::commit();
 
             return redirect()->route('store.leads.index')->with('success', 'Lead created successfully.');
         } catch (\Throwable $th) {
             DB::rollBack();
 
             return back()->withInput()->with('error', $th->getMessage());
         }
     }
 
     public function quotationForm(Lead $lead)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $categories = ProductCategory::orderBy('strCategoryName', 'asc')->get();
         $products = Product::orderBy('strProductName', 'asc')->get();
 
         return view('store-manager.leads.quotation', compact('lead', 'categories', 'products'));
     }
 
     public function saveQuotation(Request $request, Lead $lead)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $data = $request->validate([
             'iProductCategoryId' => 'required|exists:product_categories,iCategoryId',
             'iProductId' => 'required|exists:products,iProductId',
             'decHeight' => 'required|numeric|min:0',
             'decWidth' => 'required|numeric|min:0',
             'decRatePerSqft' => 'required|numeric|min:0',
            'followup_date' => 'required|date',
            'is_fitting_charge_extra' => 'required|in:0,1',
            'iFittingCharges' => 'nullable|numeric|min:0|required_if:is_fitting_charge_extra,1',
         ]);
 
         DB::beginTransaction();
 
         try {
             $sqft = $data['decHeight'] * $data['decWidth'];
             $amount = $sqft * $data['decRatePerSqft'];
            $fittingCharges = (float) ($data['iFittingCharges'] ?? 0);
 
            $quotation = LeadQuotation::updateOrCreate(
                ['iLeadId' => $lead->iLeadId],
                [
                    'iProductCategoryId' => $data['iProductCategoryId'],
                    'iProductId' => $data['iProductId'],
                    'decHeight' => $data['decHeight'],
                    'decWidth' => $data['decWidth'],
                    'decRatePerSqft' => $data['decRatePerSqft'],
                    'decTotalSqft' => $sqft,
                    'iAmount' => $amount,
                ]
            );
 
             $lead->update([
                'iQuotationId' => $quotation->iQuotationId,
                'iLeadAmount' => $amount + $fittingCharges,
                'iCurrentLeadStatus' => LeadWorkflow::STATUS_QUOTATION_SENT,
                'NetFollowupdate' => $data['followup_date'],
                'isFittingRequired' => $data['is_fitting_charge_extra'],
                'isFittingChargeIncluded' => $data['is_fitting_charge_extra'],
                'iFittingCharges' => $fittingCharges,
            ]);

            LeadHistory::create([
                'iLeadId' => $lead->iLeadId,
                'strComments' => 'Quotation generated.',
                'NetFolloupwdate' => $data['followup_date'],
                'iStatus' => LeadWorkflow::STATUS_QUOTATION_SENT,
                'iEnterBy' => auth()->id(),
                'EntryDate' => now(),
             ]);
 
             DB::commit();
 
            return redirect()->route('store.leads.histories.index', $lead->iLeadId)->with('success', 'Quotation generated successfully.');
         } catch (\Throwable $th) {
             DB::rollBack();
 
             return back()->withInput()->with('error', $th->getMessage());
         }
     }
 
     public function updateStatus(Request $request, Lead $lead)
     {
        abort_unless(LeadWorkflow::canAccessLead(auth()->user(), $lead), 403);

         $data = $request->validate([
            'iStatus' => 'required|string|in:' . implode(',', LeadWorkflow::allStatuses()),
            'NetFollowupdate' => 'nullable|date',
            'strComments' => 'required|string',
         ]);
 
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        $followupRequired = in_array($data['iStatus'], LeadWorkflow::followupRequiredStatuses(), true);

        if ($followupRequired && empty($data['NetFollowupdate'])) {
            return back()->withInput()->withErrors(['NetFollowupdate' => 'Next follow up date is required for the selected status.']);
        }

        if ($roleSlug !== 'storemanager' && ! in_array($data['iStatus'], LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead), true)) {
            return back()->withInput()->with('error', 'You cannot move this lead to the selected status.');
        }

         DB::beginTransaction();
 
         try {
             $lead->update([
                 'iCurrentLeadStatus' => $data['iStatus'],
                 'NetFollowupdate' => $data['NetFollowupdate'] ?? null,
             ]);
 
            LeadHistory::create([
                 'iLeadId' => $lead->iLeadId,
                'strComments' => $data['strComments'],
                 'NetFolloupwdate' => $data['NetFollowupdate'] ?? null,
                 'iStatus' => $data['iStatus'],
                 'iEnterBy' => auth()->id(),
                 'EntryDate' => now(),
             ]);
 
             DB::commit();
 

            return redirect()->route('store.leads.histories.index', $lead->iLeadId)->with('success', 'Lead status updated successfully.');
         } catch (\Throwable $th) {
             DB::rollBack();
 
             return back()->with('error', $th->getMessage());
         }
     }

     public function quotationView(Lead $lead)
     {
        abort_unless(LeadWorkflow::canAccessLead(auth()->user(), $lead) || optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $lead->load([
             'customer',
             'quotation.category',
             'quotation.product',
         ]);
 
        if (! $lead->quotation) {
             return redirect()->route('store.leads.index')->with('error', 'Quotation not found for this lead.');
         }
 
         return view('store-manager.leads.quotation-view', compact('lead'));
     }
 }
