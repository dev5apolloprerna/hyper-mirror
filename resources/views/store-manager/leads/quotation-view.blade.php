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
                                <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <a href="{{ route('store.leads.quotation-pdf', $lead->iLeadId) }}"
                                    class="btn btn-danger btn-sm" target="_blank">
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

                        @php
                            $DeliveryCharges = (float) ($lead->delivery_charges ?? 0);
                            $packingCharges = (float) ($lead->packing_charges ?? 0);
                            $subtotalAmount = (float) $lead->quotations->sum('iAmount');
                            $totalSqft = (float) $lead->quotations->sum('decTotalSqft');
                            $totalQty = (float) $lead->quotations->sum('quantity');
                            $fittingCharges = (float) ($lead->iFittingCharges ?? 0);
                            $discountAmount =
                                (int) ($lead->isDiscountApplicable ?? 0) === 1
                                    ? (float) ($lead->decDiscountAmount ?? 0)
                                    : 0;
                            $amountAfterDiscount = max($subtotalAmount + $fittingCharges + $DeliveryCharges + $packingCharges - $discountAmount, 0);
                            $gstAmount =
                                (int) ($lead->isGstApplicable ?? 0) === 1
                                    ? (float) ($lead->decGstAmount ?? $amountAfterDiscount * 0.18)
                                    : 0;
                        @endphp



                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">Customer Details</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Customer Name</th>
                                        <td>{{ $lead->customer->strCustomer ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td>{{ $lead->customer->strMobile ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>GST No.</th>
                                        <td>{{ $lead->customer->gst_no ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $lead->customer->strAddress ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Site Address</th>
                                        <td>{{ $lead->SiteAddress ?? '' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">Lead Details</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Lead No</th>
                                        <td>{{ $lead->strLeadNo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $lead->iCurrentLeadStatus }}</td>
                                    </tr>
                                    <tr>
                                        <th>Measurement Required</th>
                                        <td>{{ $lead->IsMeasureMentRequired == 1 ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Only Fitting Quotation</th>
                                        <td>{{ (int) ($lead->isFittingLeadOnly ?? 0) === 1 ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fitting Required</th>
                                        <td>{{ (int) ($lead->isFittingRequired ?? 0) === 1 ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fitting Charge Type</th>
                                        <td>
                                            @if ((int) ($lead->isFittingRequired ?? 0) !== 1)
                                                N/A
                                            @else
                                                {{ (int) ($lead->isFittingChargeIncluded ?? 0) === 1 ? 'Included' : 'Extra' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($lead->MeasurementVisitDate)
                                        <tr>
                                            <th>Measurement Visit Date</th>
                                            <td>{{ \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($lead->NetFollowupdate)
                                        <tr>
                                            <th>Next Follow Up</th>
                                            <td>{{ \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endif
                                    @if ($canViewFinancial && (int) ($lead->isDiscountApplicable ?? 0) === 1)
                                        <tr>
                                            <th>Discount</th>
                                            <td>₹{{ number_format($discountAmount, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($canViewFinancial && (int) ($lead->isGstApplicable ?? 0) === 1)
                                        <tr>
                                            <th>GST (18%)</th>
                                            <td>₹{{ number_format($gstAmount, 2) }}</td>
                                        </tr>
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
                                                <th>Width</th>
                                                <th>Height</th>
                                                @if ($canViewFinancial)
                                                    <th>Total Sqft</th>
                                                    <th>Rate/Sqft</th>
                                                    <th>Amount</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lead->quotations as $index => $quotationItem)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $quotationItem->category->strCategoryName ?? '' }}</td>
                                                    <td>{{ $quotationItem->product->strProductName ?? '' }}</td>
                                                    <td>{{ optional($quotationItem->shape)->shape_title ?? '—' }}</td>
                                                    <td>{{ optional($quotationItem->feature)->feature_name ?? '—' }}</td>
                                                    <td>{{ $quotationItem->unit_of_measurement ?? '—' }}</td>
                                                    <td>{{ $quotationItem->quantity ?? 1 }}</td>
                                                    <td>{{ $quotationItem->decWidth }}</td>
                                                    <td>{{ $quotationItem->decHeight }}</td>
                                                    @if ($canViewFinancial)
                                                        <td>{{ number_format((float) ($quotationItem->decTotalSqft ?? 0), 2) }}
                                                        </td>
                                                        <td>{{ number_format((float) ($quotationItem->decRatePerSqft ?? 0), 2) }}
                                                        </td>
                                                        <td>{{ number_format((float) ($quotationItem->iAmount ?? 0), 2) }}
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        @if ($canViewFittingCharges && $fittingCharges > 0)
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6"></th>
                                                    <th colspan="2" class="text-end">Fitting Charges</th>
                                                    <th class="text-end">
                                                        ₹{{ number_format($fittingCharges, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                        @if ($dispatch && $DeliveryCharges > 0)
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6"></th>
                                                    <th colspan="2" class="text-end">Delivery Charges</th>
                                                    <th class="text-end">
                                                        ₹{{ number_format($DeliveryCharges, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                        @if ($canViewFinancial)
                                            <tfoot>
                                                <tr>
                                                    <th colspan="9" class="text-end">Total Qty</th>
                                                    <th colspan="3">{{ number_format($totalQty, 0) }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="9" class="text-end">Total Sqft</th>
                                                    <th colspan="3">{{ number_format($totalSqft, 2) }}</th>
                                                </tr>

                                                <tr>
                                                    <th colspan="{{ 9 }}" class="text-end">Subtotal</th>
                                                    <th colspan="3">
                                                        ₹{{ number_format((float) $lead->quotations->sum('iAmount'), 2) }}
                                                    </th>
                                                </tr>
                                                @if ($fittingCharges > 0)
                                                    <tr>
                                                        <th colspan="9" class="text-end">Fitting Charges</th>
                                                        <th colspan="3">₹{{ number_format($fittingCharges, 2) }}</th>
                                                    </tr>
                                                @endif
                                                @if ($DeliveryCharges > 0)
                                                    <tr>
                                                        <th colspan="9" class="text-end">Delivery Charges</th>
                                                        <th colspan="3">₹{{ number_format($DeliveryCharges, 2) }}</th>
                                                    </tr>
                                                @endif
                                                @if ($packingCharges > 0)
                                                    <tr>
                                                        <th colspan="9" class="text-end">Packing Charges</th>
                                                        <th colspan="3">₹{{ number_format($packingCharges, 2) }}</th>
                                                    </tr>
                                                @endif
                                                @if ((int) ($lead->isDiscountApplicable ?? 0) === 1)
                                                    <tr>
                                                        <th colspan="9" class="text-end">Discount</th>
                                                        <th colspan="3">- ₹{{ number_format($discountAmount, 2) }}</th>
                                                    </tr>
                                                @endif
                                                @if ((int) ($lead->isGstApplicable ?? 0) === 1)
                                                    <tr>
                                                        <th colspan="9" class="text-end">GST (18%)</th>
                                                        <th colspan="3">₹{{ number_format($gstAmount, 2) }}</th>
                                                    </tr>
                                                @endif
                                                <tr class="table-success">
                                                    <th colspan="9" class="text-end">Grand Total</th>
                                                    <th colspan="3">
                                                        ₹{{ number_format((float) ($lead->iLeadAmount ?? $amountAfterDiscount + $gstAmount), 2) }}
                                                    </th>
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

            .btn,
            .page-title-right,
            .main-footer,
            .app-menu,
            header,
            nav {
                display: none !important;
            }

            .main-content,
            .page-content,
            .container-fluid,
            .card,
            .card-body {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            table {
                width: 100% !important;
            }
        }
    </style>
@endsection
