<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadQuotation;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'quotation'])->latest('iLeadId');

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

        return view('store-manager.leads.index', compact('leads'));
    }

    public function create()
    {
        return view('store-manager.leads.create');
    }

    public function checkCustomer(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $customer = Customer::where('strMobile', $request->mobile)->first();

        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'strMobile' => 'required|digits:10',
            'strCustomer' => 'required|string|max:100',
            'strAddress' => 'nullable|string',
            'SiteAddress' => 'nullable|string',
            'IsMeasureMentRequired' => 'required|in:0,1',
            'MeasurementVisitDate' => 'nullable|date|required_if:IsMeasureMentRequired,1',
            'quotation_date' => 'nullable|date',
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

            if (!$customer->wasRecentlyCreated) {
                $customer->update([
                    'strCustomer' => $data['strCustomer'],
                    'strAddress' => $data['strAddress'] ?? null,
                ]);
            }

            $fy = now()->format('y') . now()->addYear()->format('y');

            $leadCount = Lead::where('iCurrentYearLeadId', $fy)->count() + 1;
            $leadNo = $fy . '-' . str_pad($leadCount, 4, '0', STR_PAD_LEFT);

            $isMeasurementRequired = (int) $data['IsMeasureMentRequired'] === 1;

            $status = $isMeasurementRequired ? 'Measurement Required' : 'Quotation Pending';
            $nextFollowDate = $isMeasurementRequired
                ? ($data['MeasurementVisitDate'] ?? null)
                : ($data['quotation_date'] ?? null);

            Lead::create([
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

            DB::commit();

            return redirect()->route('store.leads.index')->with('success', 'Lead created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function quotationForm(Lead $lead)
    {
        $categories = ProductCategory::orderBy('strCategoryName', 'asc')->get();
        $products = Product::orderBy('strProductName', 'asc')->get();

        return view('store-manager.leads.quotation', compact('lead', 'categories', 'products'));
    }

    public function saveQuotation(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'iProductCategoryId' => 'required|exists:product_categories,iCategoryId',
            'iProductId' => 'required|exists:products,iProductId',
            'decHeight' => 'required|numeric|min:0',
            'decWidth' => 'required|numeric|min:0',
            'decRatePerSqft' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $sqft = $data['decHeight'] * $data['decWidth'];
            $amount = $sqft * $data['decRatePerSqft'];

            $quotation = LeadQuotation::create([
                'iLeadId' => $lead->iLeadId,
                'iProductCategoryId' => $data['iProductCategoryId'],
                'iProductId' => $data['iProductId'],
                'decHeight' => $data['decHeight'],
                'decWidth' => $data['decWidth'],
                'decRatePerSqft' => $data['decRatePerSqft'],
                'decTotalSqft' => $sqft,
                'iAmount' => $amount,
            ]);

            $lead->update([
                'iQuotationId' => $quotation->iQuotationId,
                'iLeadAmount' => $amount,
                'iCurrentLeadStatus' => 'Quotation Sent',
                'NetFollowupdate' => Carbon::now()->addDays(3)->toDateString(),
            ]);

            DB::commit();

            return redirect()->route('store.leads.index')->with('success', 'Quotation generated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'iStatus' => 'required|string|max:50',
            'NetFollowupdate' => 'nullable|date',
            'strComments' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $lead->update([
                'iCurrentLeadStatus' => $data['iStatus'],
                'NetFollowupdate' => $data['NetFollowupdate'] ?? null,
            ]);

            DB::table('lead_histories')->insert([
                'iLeadId' => $lead->iLeadId,
                'strComments' => $data['strComments'] ?? null,
                'NetFolloupwdate' => $data['NetFollowupdate'] ?? null,
                'iStatus' => $data['iStatus'],
                'iEnterBy' => auth()->id(),
                'EntryDate' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Lead status updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }
    public function quotationView(Lead $lead)
    {
        $lead->load([
            'customer',
            'quotation.category',
            'quotation.product',
        ]);

        if (!$lead->quotation) {
            return redirect()->route('store.leads.index')->with('error', 'Quotation not found for this lead.');
        }

        return view('store-manager.leads.quotation-view', compact('lead'));
    }
}
