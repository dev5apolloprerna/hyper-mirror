<?php

namespace App\Http\Controllers\AccountUser;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LedgerHelper;

class AccountPaymentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('account_payments_collection')
                ->leftJoin('users', 'users.id', '=', 'account_payments_collection.emp_id')
                ->whereNull('account_payments_collection.is_Delete_recode')
                ->select(
                    'account_payments_collection.*',
                    'users.first_name',
                    'users.last_name',
                    'users.mobile_number'
                )
                ->orderBy('account_payments_collection.account_payment_id', 'desc');
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('users.first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('users.mobile_number', 'like', '%' . $request->search . '%');
                });
            }

            $payments = $query->paginate(10);
            $totalCollection = DB::table('account_payments_collection')
                ->whereNull('is_Delete_recode')
                ->sum('amount');
            $userBalances = DB::table('account_payments_collection')
                ->whereNull('is_Delete_recode')
                ->select('emp_id', DB::raw('SUM(amount) as total_paid'))
                ->groupBy('emp_id')
                ->get();

            return view('Accountuser.paymentcollection.index', compact('payments', 'totalCollection', 'userBalances'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function Create(Request $request)
    {
        try {
            $getuser = DB::table('users')
                ->whereNotIn('role_id', [1])
                ->whereNotIn('iRoalId', [6])
                ->get();

            return view('Accountuser.paymentcollection.create', compact('getuser'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function Store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required',
            'notes' => 'nullable|string'
        ]);
        DB::beginTransaction();

        try {
            $auth = Auth::user()->id;
            $paymentmode = strtolower($request->payment_mode) === 'cash' ? 0 : 1;
            $getdataid = DB::table('account_payments_collection')->insertGetId([
                'user_id'      => $auth,
                'amount'       => $request->amount,
                'payment_mode' => $paymentmode,
                'payment_date' => now(),
                'comment'        => $request->notes,
                'emp_id'      =>  $request->user_id,
                'created_at'   => now(),
            ]);
            if ($paymentmode == 0) {
                $Cr_emp_id = $auth;
                $invoices_Id  =  $getdataid;
                $amount = $request->amount;
                $usertype = "Account";
                $dr_emp_id = $request->user_id;

                $response = LedgerHelper::manageLedger($Cr_emp_id, $invoices_Id, $amount, $usertype, $dr_emp_id);
                if (!$response['status']) {
                    DB::rollback();
                    return back()->with('error', $response['message'])->withInput();
                }
            }
            DB::commit();
            return redirect()->route('Accountuser.Accountpayments')
                ->with('success', 'Payment added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function delete($id = null, $emp_id = null)
    {
        DB::beginTransaction();
        try {
            $auth = Auth::user()->id;
            $payment = DB::table('account_payments_collection')
                ->where('account_payment_id', $id)
                ->first();

            if (!$payment) {
                return back()->with('error', 'Payment not found');
            }

            $amount = $payment->amount;
            $Cr_emp_id = $payment->emp_id;
            $dr_emp_id = $auth;

            $Get_store_manager = DB::table('cash_payment_ledger')
                ->where('emp_id', $Cr_emp_id)
                ->where('UserType', 1)
                ->orderByDesc('cash_payment_ledger_id')
                ->first();

            $Open = $Get_store_manager->close ?? 0;
            $credit = $amount;
            $debit = 0;
            $Close = $Open + $amount;

            DB::table('cash_payment_ledger')->insert([
                'emp_id' => $Cr_emp_id,
                'invoices_Id' => $id,
                'open' => $Open,
                'credit' => $credit,
                'debit' => $debit,
                'close' => $Close,
                'credit_emp_id' => $dr_emp_id,
                'debit_emp_id' => 0,
                'UserType' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            $Account_get_data = DB::table('cash_payment_ledger')
                ->where('emp_id', $dr_emp_id)
                ->where('UserType', 2)
                ->orderByDesc('cash_payment_ledger_id')
                ->first();

            $AccountOpening = $Account_get_data->close ?? 0;
            $Accountcredit = 0;
            $Accountdebit = $amount;
            $AccountClose = $AccountOpening - $amount;

            DB::table('cash_payment_ledger')->insert([
                'emp_id' => $dr_emp_id,
                'invoices_Id' => $id,
                'open' => $AccountOpening,
                'credit' => $Accountcredit,
                'debit' => $Accountdebit,
                'close' => $AccountClose,
                'credit_emp_id' => 0,
                'debit_emp_id' => $dr_emp_id,
                'UserType' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('account_payments_collection')
                ->where('account_payment_id', $id)
                ->update([
                    'is_Delete_recode' => now()
                ]);
            DB::commit();
            return back()->with('success', 'Payment deleted and ledger reversed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }
}
