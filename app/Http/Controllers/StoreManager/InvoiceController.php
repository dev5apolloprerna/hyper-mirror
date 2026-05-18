<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Showroom;
use App\Models\ProductShape;
use App\Models\ProductFeature;
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
        $isAdmin = $this->isAdminLike();
        $canManageInvoices = $this->isStoreManager();

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

        $assignedShowroomIds = $isAdmin
            ? collect()
            : auth()->user()->showrooms()->pluck('showrooms.iShowroomId');

        /*        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                    ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));*/
        $query = Invoice::with(['showroom', 'createdBy', 'items.category', 'items.product', 'items.shape', 'items.feature'])
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
            'todayUnpaidAmount',
            'canManageInvoices'
        ));
    }

    // ── Create form ──────────────────────────────────────────────────────────
    public function create()
    {
        //$this->authorise();
        abort_unless($this->isStoreManager(), 403);

        $isAdmin = $this->isAdminLike();
        $assignedShowroomIds = $isAdmin
            ? collect()
            : auth()->user()->showrooms()->pluck('showrooms.iShowroomId');


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

                $shapes = ProductShape::where('iStatus', 1)->where('isDelete', 0)->orderBy('shape_title')->get();
        $features = ProductFeature::where('iStatus', 1)->where('isDelete', 0)->orderBy('feature_name')->get();

        return view('store-manager.invoice.create', compact('showrooms', 'categories', 'products', 'shapes', 'features', 'defaultShowroomId'));
    }

    // ── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {

        abort_unless($this->isStoreManager(), 403);
        $isAdmin = $this->isAdminLike();

        $assignedShowroomIds = $isAdmin
            ? []
            : auth()->user()->showrooms()->pluck('showrooms.iShowroomId')->all();

        $request->validate([
            'iShowroomId'              => array_filter([
                'required',
                'exists:showrooms,iShowroomId',
                !$isAdmin ? Rule::in($assignedShowroomIds) : null,
            ]),
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
            'items.*.item_remark'      => 'nullable|string|max:255',
            'items.*.width'            => 'nullable|numeric|min:0',
            'items.*.height'           => 'nullable|numeric|min:0',
            'items.*.unit_of_measurement' => 'required|in:inch,MM,Feet',
            'items.*.shape_id'            => 'required|exists:product_shape,shape_id',
            'items.*.feature_id'          => 'required|exists:product_feature,feature_id',
            'items.*.decRatePerSqft'      => 'required|numeric|min:0',
            'items.*.calculation_multiple' => ['required', Rule::in([3, 6])],
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
                $width  = (float) ($row['width'] ?? 1);
                $height = (float) ($row['height'] ?? 1);
                 $rate  = (float) ($row['decRatePerSqft'] ?? 0);
                $unit = (string) ($row['unit_of_measurement'] ?? 'inch');
                $multiple = (int) ($row['calculation_multiple'] ?? 3);

                $toInch = static function (float $value, string $u): float {
                    return match ($u) {
                        'MM' => $value / 25.4,
                        'Feet' => $value * 12,
                        default => $value,
                    };
                };

                $normalizedHeight = ceil($toInch($height, $unit) / ($multiple === 6 ? 6 : 3)) * ($multiple === 6 ? 6 : 3);
                $normalizedWidth = ceil($toInch($width, $unit) / ($multiple === 6 ? 6 : 3)) * ($multiple === 6 ? 6 : 3);
                $sqftPerPiece = ($normalizedWidth / 12) * ($normalizedHeight / 12);
                $sqft = $qty * $sqftPerPiece;
                $amount = round($sqft * $rate);


                $totalAmount += $amount;
                InvoiceItem::create([
                    'iInvoiceId'  => $invoice->iInvoiceId,
                    'iCategoryId' => $row['iCategoryId'],
                    'iProductId'  => $row['iProductId'],
                    'quantity'    => $qty,
                    'width'       => $width,
                    'height'      => $height,
                    'shape_id' => $row['shape_id'],
                    'feature_id' => $row['feature_id'],
                    'decRatePerSqft' => $rate,
                    'decTotalSqft' => $sqft,
                    'iAmount'     => $amount,
                    'item_remark' => $row['item_remark'] ?? null,
                    'unit_of_measurement' => $unit,
                    'calculation_multiple' => $multiple,

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
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product', 'items.shape', 'items.feature']);
        $invoicePdfSetting = InvoicePdfSetting::query()->first();

        $pdf = Pdf::loadView('store-manager.invoice.pdf', compact('invoice', 'invoicePdfSetting'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($invoice->strInvoiceNo . '.pdf');
    }


    // ── View single invoice ──────────────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $this->authorise();
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product', 'items.shape', 'items.feature']);
        return view('store-manager.invoice.show', compact('invoice'));
    }
    public function updatePayment(Request $request, Invoice $invoice)
    {
        abort_unless($this->isStoreManager(), 403);

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
        abort_unless($this->isStoreManager(), 403);
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
        abort_unless($this->isStoreManager() || $this->isAdminLike(), 403);
    }

    private function isStoreManager(): bool
    {
        return optional(auth()->user()->crmRole)->slug === 'storemanager';
    }

    private function isAdminLike(): bool
    {
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        return blank($roleSlug) || $roleSlug === 'admin';
    }
}