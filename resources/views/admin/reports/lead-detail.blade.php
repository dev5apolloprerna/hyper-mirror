@extends('layouts.app')
@section('title', 'Lead Detail')

@section('styles')
<style>
    .latest-quotation-card .card-header {
        padding: 1.25rem 1.5rem;
    }

    .latest-quotation-table-wrap {
        padding: 1.25rem;
    }

    .latest-quotation-table {
        min-width: 1250px;
    }

    .latest-quotation-table th,
    .latest-quotation-table td {
        padding: 0.75rem 0.65rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .latest-quotation-table .number-cell {
        white-space: nowrap;
    }

    .latest-quotation-table .remarks-cell {
        min-width: 320px;
        white-space: normal;
    }

    .latest-quotation-table tfoot th {
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }

    .latest-quotation-table tfoot .total-label {
        padding-right: 1.25rem;
    }

    .latest-quotation-table tfoot .total-value {
        padding-left: 1.25rem;
        min-width: 180px;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lead Detail - {{ $lead->strLeadNo }}</h4>
        <a href="{{ route('admin.reports.leads') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Customer:</strong><br>{{ optional($lead->customer)->strCustomer ?? '—' }}</div>
            <div class="col-md-3"><strong>Mobile:</strong><br>{{ optional($lead->customer)->strMobile ?? '—' }}</div>
            <div class="col-md-3"><strong>Showroom:</strong><br>{{ optional($lead->showroom)->strShowRoomName ?? '—' }}</div>
            <div class="col-md-3"><strong>Status:</strong><br>{{ $lead->iCurrentLeadStatus }}</div>
            <div class="col-md-3"><strong>Lead Amount:</strong><br>₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</div>
            <div class="col-md-3"><strong>Created By:</strong><br>{{ optional($lead->createdBy)->strUserName ?: optional($lead->createdBy)->name ?? '—' }}</div>
            <div class="col-md-3">    <strong>Created Date:</strong><br>    {{ $lead->CreatedDate ? \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') : '—' }}</div><div class="col-md-3">    <strong>Next Follow-up:</strong><br>    {{ $lead->NetFollowupdate ? \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') : '—' }}</div>
        </div>
    </div></div>

    <div class="row g-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">History Entries</div><h4>{{ $historyCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Quotation Entries</div><h4>{{ $quotationCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Payment Entries</div><h4>{{ $paymentCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Payment Received</div><h4>₹{{ number_format($paymentTotal, 2) }}</h4></div></div></div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('admin.reports.leads.histories', $lead->iLeadId) }}" class="btn btn-outline-info btn-sm">Lead History</a>
        <a href="{{ route('admin.reports.leads.quotations', $lead->iLeadId) }}" class="btn btn-outline-secondary btn-sm">Quotation History</a>
        <a href="{{ route('admin.reports.leads.payments', $lead->iLeadId) }}" class="btn btn-outline-dark btn-sm">Payment Details</a>
    </div>

      <div class="card mt-4 latest-quotation-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Latest Quotation</h5>
                @if($latestQuotationBatchId !== null)
                    <small class="text-muted">
                        Version #{{ $latestQuotationBatchId }}
                        @if(optional($latestQuotations->max('created_at'))->format('d-m-Y h:i A'))
                            &middot; {{ optional($latestQuotations->max('created_at'))->format('d-m-Y h:i A') }}
                        @endif
                    </small>
                @endif
            </div>
            <a href="{{ route('admin.reports.leads.quotations', ['lead' => $lead->iLeadId, 'batch_id' => $latestQuotationBatchId]) }}" class="btn btn-outline-primary btn-sm">
                View Quotation History
            </a>
        </div>

        @if($latestQuotations->isNotEmpty())
            <div class="latest-quotation-table-wrap">
                <div class="table-responsive border rounded">
                    <table class="table table-bordered table-striped align-middle mb-0 latest-quotation-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Shape</th>
                            <th>Feature</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Total Sqft</th>
                            <th>Rate / Sqft</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestQuotations as $quotation)
                            <tr>
                                <td class="number-cell">{{ $loop->iteration }}</td>
                                <td>{{ optional($quotation->category)->strCategoryName ?? '—' }}</td>
                                <td>{{ optional($quotation->product)->strProductName ?? '—' }}</td>
                                <td>{{ optional($quotation->shape)->shape_title ?? '—' }}</td>
                                <td>{{ optional($quotation->feature)->feature_name ?? '—' }}</td>
                                <td class="number-cell">{{ (int) ($quotation->quantity ?? 0) }}</td>
                                <td>{{ $quotation->unit_of_measurement ?? '—' }}</td>
                                <td class="number-cell">{{ number_format((float) ($quotation->decWidth ?? 0), 2) }}</td>
                                <td class="number-cell">{{ number_format((float) ($quotation->decHeight ?? 0), 2) }}</td>
                                <td class="number-cell">{{ number_format((float) ($quotation->decTotalSqft ?? 0), 2) }}</td>
                                <td class="number-cell">₹{{ number_format((float) ($quotation->decRatePerSqft ?? 0), 2) }}</td>
                                <td class="number-cell">₹{{ number_format((float) ($quotation->iAmount ?? 0), 2) }}</td>
                                <td class="remarks-cell">{{ $quotation->remarks ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="11" class="text-end total-label">Subtotal</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationSubtotal, 2) }}</th></tr>
                        <tr><th colspan="11" class="text-end total-label">Fitting Charges</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationFitting, 2) }}</th></tr>
                        <tr><th colspan="11" class="text-end total-label">Delivery Charges</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationDelivery, 2) }}</th></tr>
                        <tr><th colspan="11" class="text-end total-label">Discount</th><th colspan="2" class="total-value">- ₹{{ number_format($latestQuotationDiscount, 2) }}</th></tr>
                        <tr><th colspan="11" class="text-end total-label">Taxable Amount</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationTaxable, 2) }}</th></tr>
                        <tr><th colspan="11" class="text-end total-label">GST</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationGst, 2) }}</th></tr>
                        <tr class="table-success"><th colspan="11" class="text-end total-label">Grand Total</th><th colspan="2" class="total-value">₹{{ number_format($latestQuotationTotal, 2) }}</th></tr>
                    </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div class="card-body text-center text-muted py-4">No quotation has been created for this lead yet.</div>
        @endif
    </div>

</div></div></div>
@endsection
