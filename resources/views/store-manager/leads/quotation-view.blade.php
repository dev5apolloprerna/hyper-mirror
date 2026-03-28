@extends('layouts.app')

@section('title', 'Quotation View')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Quotation View</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="page-title-right d-flex gap-2">
                            <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('store.leads.quotation-pdf', $lead->iLeadId) }}" class="btn btn-danger btn-sm" target="_blank">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="window.print();">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" id="printableArea">
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Customer Details</h5>
                            <table class="table table-bordered">
                                <tr><th width="35%">Customer Name</th><td>{{ $lead->customer->strCustomer ?? '' }}</td></tr>
                                <tr><th>Mobile</th><td>{{ $lead->customer->strMobile ?? '' }}</td></tr>
                                <tr><th>Address</th><td>{{ $lead->customer->strAddress ?? '' }}</td></tr>
                                <tr><th>Site Address</th><td>{{ $lead->SiteAddress ?? '' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">Lead Details</h5>
                            <table class="table table-bordered">
                                <tr><th width="35%">Lead No</th><td>{{ $lead->strLeadNo }}</td></tr>
                                <tr><th>Status</th><td>{{ $lead->iCurrentLeadStatus }}</td></tr>
                                <tr><th>Measurement Required</th><td>{{ $lead->IsMeasureMentRequired == 1 ? 'Yes' : 'No' }}</td></tr>
                                @if($lead->MeasurementVisitDate)
                                <tr><th>Measurement Visit Date</th><td>{{ \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') }}</td></tr>
                                @endif
                                @if($lead->NetFollowupdate)
                                <tr><th>Next Follow Up</th><td>{{ \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Quotation Details</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Shape</th>
                                            <th>Feature</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>Height</th>
                                            <th>Width</th>
                                            @if($canViewFinancial)
                                                <th>Rate/Sqft</th>
                                                <th>Total Sqft</th>
                                                <th>Amount</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lead->quotations as $index => $quotationItem)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $quotationItem->category->strCategoryName ?? '' }}</td>
                                                <td>{{ $quotationItem->product->strProductName ?? '' }}</td>
                                                <td>{{ optional($quotationItem->shape)->shape_title ?? '—' }}</td>
                                                <td>{{ optional($quotationItem->feature)->feature_name ?? '—' }}</td>
                                                <td>{{ $quotationItem->unit_of_measurement ?? '—' }}</td>
                                                <td>{{ $quotationItem->quantity ?? 1 }}</td>
                                                <td>{{ $quotationItem->decHeight }}</td>
                                                <td>{{ $quotationItem->decWidth }}</td>
                                                @if($canViewFinancial)
                                                    <td>{{ number_format((float)($quotationItem->decRatePerSqft ?? 0), 2) }}</td>
                                                    <td>{{ number_format((float)($quotationItem->decTotalSqft ?? 0), 2) }}</td>
                                                    <td>{{ number_format((float)($quotationItem->iAmount ?? 0), 2) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @if($canViewFinancial)
                                    <tfoot>
                                        <tr>
                                            <th colspan="{{ 9 }}" class="text-end">Subtotal</th>
                                            <th colspan="3">₹{{ number_format((float) $lead->quotations->sum('iAmount'), 2) }}</th>
                                        </tr>
                                        @if((float)($lead->iFittingCharges ?? 0) > 0)
                                            <tr>
                                                <th colspan="9" class="text-end">Fitting Charges</th>
                                                <th colspan="3">₹{{ number_format((float)($lead->iFittingCharges ?? 0), 2) }}</th>
                                            </tr>
                                        @endif
                                        <tr class="table-success">
                                            <th colspan="9" class="text-end">Grand Total</th>
                                            <th colspan="3">₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <p><strong>Customer Signature</strong></p>
                            <br><br>
                            <p>____________________________</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Authorized Signature</strong></p>
                            <br><br>
                            <p>____________________________</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
@media print {
    .btn, .page-title-right, .main-footer, .app-menu, header, nav { display: none !important; }
    .main-content, .page-content, .container-fluid, .card, .card-body {
        margin: 0 !important; padding: 0 !important;
        box-shadow: none !important; border: none !important;
    }
    table { width: 100% !important; }
}
</style>
@endsection
