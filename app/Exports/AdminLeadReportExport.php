<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminLeadReportExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $leads)
    {
    }

    public function collection()
    {
        return $this->leads->map(function ($lead, $index) {
            return [
                'sr_no' => $index + 1,
                'lead_no' => $lead->strLeadNo,
                'customer_name' => optional($lead->customer)->strCustomer,
                'customer_mobile' => optional($lead->customer)->strMobile,
                'customer_type' => optional($lead->customer)->customer_type,
                'sales_person' => optional($lead->createdBy)->strUserName ?: optional($lead->createdBy)->name,
                'status' => $lead->iCurrentLeadStatus,
                'lead_amount' => (float) ($lead->iLeadAmount ?? 0),
                'quotation_entries' => $lead->quotations->count(),
                'payment_received' => (float) $lead->payments->sum('iPaidAmount'),
                'history_entries' => $lead->histories->count(),
                'created_date' => $lead->CreatedDate,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Lead No',
            'Customer Name',
            'Customer Mobile',
            'Customer Type',
            'Sales Person',
            'Status',
            'Lead Amount',
            'Quotation Entries',
            'Payment Received',
            'History Entries',
            'Created Date',
        ];
    }
}
