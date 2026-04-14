<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Showroom;
use Illuminate\Http\Request;
use App\Models\InvoicePdfSetting;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LedgerHelper;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Listing ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorise();

        /*$query = Invoice::with(['showroom', 'createdBy', 'items.category', 'items.product'])
            ->orderByDesc('iInvoiceId');

        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('InvoiceDate', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('InvoiceDate', '<=', $request->to_date);
        }*/

        $assignedShowroomIds = auth()->user()
            ->showrooms()
            ->pluck('showrooms.iShowroomId');

        /*        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                    ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));*/
        $query = Invoice::with(['showroom', 'createdBy', 'items.category', 'items.product'])
            ->when($assignedShowroomIds->isNotEmpty(), function ($q) use ($assignedShowroomIds) {
                $q->whereIn('iShowroomId', $assignedShowroomIds);
            });
        //}

        $this->applyFilters($query, $request);
        $query->orderByDesc('iInvoiceId');

        $invoices  = $query->paginate(15)->withQueryString();
        // $showrooms = Showroom::orderBy('strShowRoomName')->get();

        $showrooms = Showroom::query()
            ->when($assignedShowroomIds->isNotEmpty(), function ($q) use ($assignedShowroomIds) {
                $q->whereIn('iShowroomId', $assignedShowroomIds);
            })
            ->orderBy('strShowRoomName')
            ->get();

        // Grand totals for filtered set (no pagination)
        /*$totalQuery = Invoice::query();
        if ($request->filled('iShowroomId'))  $totalQuery->where('iShowroomId', $request->iShowroomId);
        if ($request->filled('from_date'))    $totalQuery->whereDate('InvoiceDate', '>=', $request->from_date);
        if ($request->filled('to_date'))      $totalQuery->whereDate('InvoiceDate', '<=', $request->to_date);
        if ($request->filled('search')) {
            $s = trim($request->search);
            $totalQuery->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                    ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));*/
        $totalQuery = Invoice::query()
            ->when($assignedShowroomIds->isNotEmpty(), function ($q) use ($assignedShowroomIds) {
                $q->whereIn('iShowroomId', $assignedShowroomIds);
            });
        //}

        $this->applyFilters($totalQuery, $request);

        $filteredIds    = $totalQuery->pluck('iInvoiceId');
        $grandTotal     = (float) InvoiceItem::whereIn('iInvoiceId', $filteredIds)->sum('iAmount');
        $totalInvoices  = $filteredIds->count();

        $overallUnpaid  = (float) InvoiceItem::whereIn('iInvoiceId', (clone $totalQuery)->where('payment_received', false)->pluck('iInvoiceId'))->sum('iAmount');

        $todayBaseQuery = Invoice::query()
            ->when($assignedShowroomIds->isNotEmpty(), function ($q) use ($assignedShowroomIds) {
                $q->whereIn('iShowroomId', $assignedShowroomIds);
            })
            ->when($request->filled('iShowroomId'), function ($q) use ($request) {
                $q->where('iShowroomId', $request->iShowroomId);
            })
            ->whereDate('InvoiceDate', now()->toDateString());

        $todayCashIds = (clone $todayBaseQuery)
            ->where('payment_mode', 'cash')
            ->pluck('iInvoiceId');
        $todayBankIds = (clone $todayBaseQuery)
            ->where('payment_mode', 'bank')
            ->pluck('iInvoiceId');
        $todayUnpaidIds = (clone $todayBaseQuery)
            ->where('payment_received', false)
            ->pluck('iInvoiceId');

        $todayCashAmount = (float) InvoiceItem::whereIn('iInvoiceId', $todayCashIds)->sum('iAmount');
        $todayBankAmount = (float) InvoiceItem::whereIn('iInvoiceId', $todayBankIds)->sum('iAmount');
        $todayUnpaidAmount = (float) InvoiceItem::whereIn('iInvoiceId', $todayUnpaidIds)->sum('iAmount');

        return view('store-manager.invoice.index', compact(
            'invoices',
            'showrooms',
            'grandTotal',
            'totalInvoices',
            'overallUnpaid',
            'todayCashAmount',
            'todayBankAmount',
            'todayUnpaidAmount'
        ));
    }

    // ── Create form ──────────────────────────────────────────────────────────
    public function create()
    {
        $this->authorise();

        $assignedShowroomIds = auth()->user()
            ->showrooms()
            ->pluck('showrooms.iShowroomId');

        $showrooms = Showroom::query()
            ->when($assignedShowroomIds->isNotEmpty(), function ($query) use ($assignedShowroomIds) {
                $query->whereIn('iShowroomId', $assignedShowroomIds);
            })
            ->orderBy('strShowRoomName')
            ->get();

        $defaultShowroomId = old('iShowroomId')
            ?: ($showrooms->count() === 1 ? $showrooms->first()->iShowroomId : null);


        $categories = ProductCategory::orderBy('strCategoryName')->get();
        $products   = Product::orderBy('strProductName')->get();

        return view('store-manager.invoice.create', compact('showrooms', 'categories', 'products', 'defaultShowroomId'));
    }

    // ── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {

        $this->authorise();

        $assignedShowroomIds = auth()->user()
            ->showrooms()
            ->pluck('showrooms.iShowroomId')
            ->all();

        $request->validate([
            'iShowroomId'              => ['required', 'exists:showrooms,iShowroomId', Rule::in($assignedShowroomIds)],
            'InvoiceDate'              => 'required|date',
            //'strNotes'                 => 'nullable|string|max:500',
            'customer_name'            => 'nullable|string|max:120',
            'customer_mobile'          => 'nullable|string|max:20',
            'customer_address'         => 'nullable|string|max:500',
            'strNotes'                 => 'nullable|string|max:500|required_if:payment_received,0',
            'payment_mode'             => ['required', Rule::in(['cash', 'bank'])],
            'payment_received'         => ['required', Rule::in(['0', '1'])],

            'items'                    => 'required|array|min:1',
            'items.*.iCategoryId'      => 'required|exists:product_categories,iCategoryId',
            'items.*.iProductId'       => 'required|exists:products,iProductId',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.item_remark'      => 'nullable|string|max:255',
        ], [
            'iShowroomId.in' => 'Please select your assigned showroom.',
            'strNotes.required_if' => 'Notes / Comments are mandatory when payment is pending.',
        ]);

        DB::beginTransaction();

        try {
            // Generate invoice number: INV-YYYYMM-XXXX
            $prefix    = 'INV-' . now()->format('Ym') . '-';
            $lastCount = Invoice::where('strInvoiceNo', 'like', $prefix . '%')->count();
            $invoiceNo = $prefix . str_pad($lastCount + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'strInvoiceNo' => $invoiceNo,
                'iShowroomId'  => $request->iShowroomId,
                'iCreatedBy'   => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_mobile' => $request->customer_mobile,
                'customer_address' => $request->customer_address,
                'InvoiceDate'  => $request->InvoiceDate,
                'strNotes'     => $request->strNotes,
                'status'       => 'confirmed',
                'payment_mode' => $request->payment_mode,
                'payment_received' => (bool) $request->payment_received,
                'payment_mode' => $request->payment_mode,
                'payment_received' => (bool) $request->payment_received,
            ]);
            $totalAmount = 0;
            foreach ($request->items as $row) {
                $qty    = (int)   $row['quantity'];
                $price  = (float) $row['unit_price'];
                $amount = $qty * $price;

                $totalAmount += $amount;
                InvoiceItem::create([
                    'iInvoiceId'  => $invoice->iInvoiceId,
                    'iCategoryId' => $row['iCategoryId'],
                    'iProductId'  => $row['iProductId'],
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'iAmount'     => $amount,
                    'item_remark' => $row['item_remark'] ?? null,
                ]);
            }

            $auth = Auth::user()->id;
            $invoiceIds = $invoice->iInvoiceId ?? 0;
            $paymentmode = strtolower($request->payment_mode) === 'cash' ? 0 : 1;
            if ($paymentmode == 0) {
                $Cr_emp_id = $auth;
                $invoices_Id  =  $invoiceIds;
                $amount = $totalAmount;
                $usertype = "StoreManager";
                $dr_emp_id = 0;

                $response = LedgerHelper::manageLedger($Cr_emp_id, $invoices_Id, $amount, $usertype, $dr_emp_id);
                if (!$response['status']) {
                    DB::rollback();
                    return back()->with('error', $response['message'])->withInput();
                }
            }
            DB::commit();

            return redirect()->route('store.invoice.index')
                ->with('success', "Invoice {$invoiceNo} created successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('InvoiceDate', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('InvoiceDate', '<=', $request->to_date);
        }

        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->where('payment_received', true);
            } elseif ($request->payment_status === 'unpaid') {
                $query->where('payment_received', false);
            }
        }

        if ($request->filled('search')) {
            $s = strtolower(trim($request->search));
            $query->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                    ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));

                if (in_array($s, ['paid', 'unpaid'], true)) {
                    $q->orWhere('payment_received', $s === 'paid');
                }
            });
        }
    }
    public function pdf(Invoice $invoice)
    {
        $this->authorise();
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product']);
        $invoicePdfSetting = InvoicePdfSetting::query()->first();

        $pdf = Pdf::loadView('store-manager.invoice.pdf', compact('invoice', 'invoicePdfSetting'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($invoice->strInvoiceNo . '.pdf');
    }


    // ── View single invoice ──────────────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $this->authorise();
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product']);
        return view('store-manager.invoice.show', compact('invoice'));
    }
    public function updatePayment(Request $request, Invoice $invoice)
    {
        $this->authorise();

        if ($invoice->payment_received) {
            return redirect()
                ->route('store.invoice.index', $request->query())
                ->with('error', 'Payment is already marked received. Payment details cannot be updated.');
        }

        $validated = $request->validate([
            'payment_mode' => ['required', Rule::in(['cash', 'bank'])],
            'payment_received' => ['required', Rule::in(['0', '1'])],
            'strNotes' => 'nullable|string|max:500',
        ]);

        $notes = trim((string) ($validated['strNotes'] ?? ''));

        if ($validated['payment_received'] === '0' && $notes === '' && blank($invoice->strNotes)) {
            return redirect()
                ->route('store.invoice.index', $request->query())
                ->with('error', 'Comments are mandatory while payment is pending.');
        }

        $invoice->update([
            'payment_mode' => $validated['payment_mode'],
            'payment_received' => (bool) $validated['payment_received'],
            'strNotes' => $notes !== '' ? $notes : $invoice->strNotes,
        ]);

        return redirect()
            ->route('store.invoice.index', $request->query())
            ->with('success', 'Invoice payment details updated successfully.');
    }


    // ── Delete ───────────────────────────────────────────────────────────────
    public function destroy(Invoice $invoice)
    {
        // $this->authorise();
        abort_unless(optional(auth()->user()->crmRole)->slug === 'admin', 403);
        $invoice->delete();
        return redirect()->route('store.invoice.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    // ── AJAX: products by category ───────────────────────────────────────────
    public function productsByCategory(Request $request)
    {
        $this->authorise();
        $products = Product::where('iCategoryId', $request->category_id)
            ->orderBy('strProductName')
            ->get(['iProductId', 'strProductName', 'MRP']);
        return response()->json($products);
    }

    // ── Guard ────────────────────────────────────────────────────────────────
    private function authorise(): void
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
    }
}
