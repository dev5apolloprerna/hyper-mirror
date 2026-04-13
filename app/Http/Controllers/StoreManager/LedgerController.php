<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorise();

        $query = DB::table('cash_payment_ledger as ledger')
            ->leftJoin('invoices as inv', 'inv.iInvoiceId', '=', 'ledger.invoices_Id')
            ->leftJoin('users as emp', 'emp.id', '=', 'ledger.emp_id')
            ->select([
                'ledger.cash_payment_ledger_id',
                'ledger.emp_id',
                'ledger.invoices_Id',
                'ledger.open',
                'ledger.credit',
                'ledger.debit',
                'ledger.close',
                'ledger.credit_emp_id',
                'ledger.debit_emp_id',
                'ledger.UserType',
                'ledger.created_at',
                'ledger.updated_at',
                'inv.strInvoiceNo',
                'emp.first_name as emp_first_name',
                'emp.strUserName as emp_user_name',
            ]);

        if ($request->filled('invoice_no')) {
            $invoiceNo = trim((string) $request->invoice_no);
            $query->where('inv.strInvoiceNo', 'like', "%{$invoiceNo}%");
        }

        if ($request->filled('from_date')) {
            $query->whereDate('ledger.created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('ledger.created_at', '<=', $request->to_date);
        }

        if ($request->filled('user_type')) {
            $query->where('ledger.UserType', $request->user_type);
        }

        $ledgerEntries = $query
            ->orderByDesc('ledger.created_at')
            ->paginate(20)
            ->withQueryString();

        $userTypes = DB::table('cash_payment_ledger')
            ->select('UserType')
            ->whereNotNull('UserType')
            ->distinct()
            ->orderBy('UserType')
            ->pluck('UserType');

        return view('store-manager.ledger.index', compact('ledgerEntries', 'userTypes'));
    }

    private function authorise(): void
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
    }
}
