<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class LedgerHelper
{
    public static function manageLedger($Cr_emp_id, $invoiceId, $amount, $usertype, $dr_emp_id)
    {

        DB::beginTransaction();
        try {
            $amount = (float) $amount;
            if ($amount <= 0) {
                return [
                    'status' => false,
                    'message' => 'Invalid amount'
                ];
            }
            if ($usertype == "StoreManager") {
                if ($Cr_emp_id <= 0) {
                    return [
                        'status' => false,
                        'message' => 'Store Manager ID is required'
                    ];
                }
                $fromLast = DB::table('cash_payment_ledger')
                    ->where('emp_id', $Cr_emp_id)
                    ->where('UserType', 1)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();
                $Open = $fromLast->close ?? 0;
                $Close = $Open + $amount;

                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $Cr_emp_id,
                    'invoices_Id' => $invoiceId,
                    'open' => $Open,
                    'credit' => $amount,
                    'debit' => 0,
                    'close' => $Close,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => $dr_emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 1,
                ]);
            } elseif ($usertype == "Account") {

                if ($dr_emp_id <= 0 || $Cr_emp_id <= 0) {
                    return [
                        'status' => false,
                        'message' => 'Store Manager ID and Account ID are required'
                    ];
                }
                $Get_store_manager = DB::table('cash_payment_ledger')
                    ->where('emp_id', $dr_emp_id)
                    ->where('UserType', 1)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();

                $Open = $Get_store_manager->close ?? 0;
                $credit = 0;
                $debit = $amount ?? 0;
                $Close =  $Open - $amount ?? 0;

                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $dr_emp_id,
                    'invoices_Id' => $invoiceId,
                    'open' => $Open,
                    'credit' => $credit,
                    'debit' => $debit,
                    'close' => $Close,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => $dr_emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 1,
                ]);

                $Account_get_data = DB::table('cash_payment_ledger')
                    ->where('emp_id', $Cr_emp_id)
                    ->where('UserType', 2)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();

                $AccountOpening = $Account_get_data->close ?? 0;
                $Accountcredit = $amount ?? 0;
                $Accountdebit = 0;
                $AccountClose = $AccountOpening + $amount ?? 0;

                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $Cr_emp_id,
                    'invoices_Id' => $invoiceId,
                    'open' => $AccountOpening,
                    'credit' => $Accountcredit,
                    'debit' => $Accountdebit,
                    'close' => $AccountClose,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => $dr_emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 2,
                ]);
            } elseif ($usertype == "Admin") {

                if ($dr_emp_id <= 0 || $Cr_emp_id <= 0) {
                    return [
                        'status' => false,
                        'message' => 'Account ID and Admin ID are required'
                    ];
                }
                $AccountData = DB::table('cash_payment_ledger')
                    ->where('emp_id', $dr_emp_id)
                    ->where('UserType', 2)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();

                $Open = $AccountData->close ?? 0;
                $credit = 0;
                $debit = $amount ?? 0;
                $Close =  $Open - $amount ?? 0;

                // 2. Debit entry (Money going OUT from fromUser)
                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $dr_emp_id,
                    'invoices_Id' => $invoiceId,
                    'open' => $Open,
                    'credit' => $credit,
                    'debit' => $debit,
                    'close' => $Close,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => $dr_emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 2,
                ]);

                $AdminData = DB::table('cash_payment_ledger')
                    ->where('emp_id', $Cr_emp_id)
                    ->where('UserType', 3)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();

                $AdminOpening = $AdminData->close ?? 0;
                $Admincredit = $amount ?? 0;
                $Admindebit = 0;
                $AdminClose = $AdminOpening + $amount ?? 0;

                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $Cr_emp_id,
                    'invoices_Id' => $invoiceId,
                    'open' => $AdminOpening,
                    'credit' => $Admincredit,
                    'debit' => $Admindebit,
                    'close' => $AdminClose,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => $dr_emp_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 3,
                ]);
            }
            DB::commit();
            return [
                'status' => true,
                'message' => 'Ledger entry successful'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
