<?php

namespace App\Exports;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PartyReportExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $partySummary)
    {
    }

    public function collection(): Collection
    {
        return $this->partySummary->map(function (array $party) {
            return [
                'sales_manager_name' => $party['sales_manager_name'],
                'party_name' => $party['party_name'],
                'mobile' => $party['mobile'],
                'total_amount' => (float) $party['total_amount'],
                'approved_amount' => (float) $party['approved_amount'],
                'approved_lead_count' => (int) $party['approved_lead_count'],
                'paid_amount' => (float) $party['paid_amount'],
                'unpaid_amount' => (float) $party['unpaid_amount'],
                'lead_count' => (int) $party['lead_count'],
                'payment_entry_count' => (int) $party['payment_entry_count'],
                'last_payment_date' => !empty($party['last_payment_date'])
                    ? Carbon::parse($party['last_payment_date'])->format('d-m-Y')
                    : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sales Manager Name',
            'Party Name',
            'Mobile No',
            'Total Amount',
            'Quotation Approved Amount',
            'Approved Lead Count',
            'Paid Amount',
            'Unpaid Amount',
            'Lead Count',
            'Payment Entries',
            'Last Payment Date',
        ];
    }
}
