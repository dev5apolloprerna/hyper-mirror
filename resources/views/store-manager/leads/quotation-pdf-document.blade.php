<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $lead->strLeadNo }}</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #2b2f33;
        }

        /* centered header */
         .header-wrap {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #d1d5db;
    }

    .header-logo {
        width: 150px;
        height: auto;
        display: block;
        margin: 0 auto 6px;
    }

    .company-meta {
        width: 75%;
        margin: 2px auto 0;
        font-size: 10px;
        color: #374151;
        line-height: 1.5;
        text-align: center;
        word-break: break-word;
    }

    .company-meta strong {
        font-weight: 700;
    }

    .doc-subtitle {
        font-size: 12px;
        margin-top: 6px;
        text-align: center;
        font-weight: 700;
    }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            border: 1px solid #cfcfcf;
            padding: 6px 8px;
        }

        .label {
            width: 20%;
            background: #f5f5f5;
            font-weight: 700;
        }

        .party-wrap {
            margin-top: 12px;
        }

        .party-table td {
            border: 1px solid #cfcfcf;
            vertical-align: top;
            padding: 8px;
        }

        .party-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .section-title {
            margin-top: 14px;
            margin-bottom: 6px;
            font-weight: 700;
            font-size: 12px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #cfcfcf;
            padding: 6px 5px;
            vertical-align: top;
        }

        .items-table th {
            background: #1f2937;
            color: #ffffff;
            font-size: 10px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .detail-title {
            background: #e5e7eb;
            border: 1px solid #cfcfcf;
            padding: 6px 8px;
            font-weight: 700;
            font-size: 11px;
        }

        .detail-body {
            border: 1px solid #cfcfcf;
            border-top: 0;
            padding: 8px;
            font-size: 10px;
            line-height: 1.5;
        }

        .detail-body p {
            margin: 0 0 6px;
        }

        .detail-body ul,
        .detail-body ol {
            margin: 0 0 6px 18px;
            padding: 0;
        }

        .summary-terms-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .summary-terms-table td {
            vertical-align: top;
        }

        .summary-cell {
            width: 42%;
            padding-left: 14px;
        }

        .terms-cell {
            width: 58%;
        }

        .summary-table td {
            border: 1px solid #cfcfcf;
            padding: 6px 8px;
        }

        .summary-table .total td {
            font-weight: 700;
            background: #f5f5f5;
        }

        .signatures {
            width: 100%;
            margin-top: 80px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signatures td {
            width: 50%;
            vertical-align: top;
        }

        .left-sign {
            text-align: left;
        }

        .right-sign {
            text-align: right;
        }

        .sign-line {
            width: 180px;
            border-top: 1px solid #888;
            margin-bottom: 8px;
        }

        .left-sign .sign-line {
            margin-left: 0;
            margin-right: auto;
        }

        .right-sign .sign-line {
            margin-left: auto;
            margin-right: 0;
        }

        .sign-text-left {
            width: 100%;
            text-align: left;
        }

        .sign-text-right {
            width: 100%;
            text-align: right;
        }
         .company-meta {
            font-size: 10px;
            margin-top: 2px;
            text-align: center;
            color: #374151;
            line-height: 1.35;
        }
    </style>
</head>

<body>
    @php
        $hasRemarksColumn = $lead->quotations->pluck('remarks')->filter()->isNotEmpty();

        $subtotalAmount = (float) $lead->quotations->sum('iAmount');
        $totalSqft = (float) $lead->quotations->sum('decTotalSqft');
        $totalQty = (float) $lead->quotations->sum('quantity');
        $fittingCharges = (float) ($lead->iFittingCharges ?? 0);
        $deliveryCharges = (float) ($lead->delivery_charges ?? 0);
        $packingCharges = (float) ($lead->packing_charges ?? 0);
        $discountAmount = (float) ($lead->decDiscountAmount ?? 0);
        $amountAfterDiscount = max($subtotalAmount + $fittingCharges + $deliveryCharges + $packingCharges - $discountAmount, 0);
        $gstAmount =
            (int) ($lead->isGstApplicable ?? 0) === 1
                ? (float) ($lead->decGstAmount ?? $amountAfterDiscount * 0.18)
                : 0;
        $netAmount = $amountAfterDiscount;
        $logoPath = base_path('assets/images/logo.png');
        $termsAndConditions = trim((string) optional($invoicePdfSetting ?? null)->terms_and_conditions);
        $bankDetails = trim((string) optional($invoicePdfSetting ?? null)->bank_details);
       
        $companyGstNo = "GSTIN: 24BIQPG6204F1ZH, State: 24-Gujarat";
        $companyAddress ="10, Sahyog Estate, Behind Anand Restaurant, Isanpur, A’bad 382443
 Email: hypermirror01@gmail.com";
    @endphp

    @php
        $logoPath = base_path('assets/images/logo.png');
    @endphp

    <div class="header-wrap">
    @if (file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Company Logo" class="header-logo">
    @endif

    @if (!empty($companyGstNo))
        <div class="company-meta">
            <strong>GST No:</strong> {{ $companyGstNo }}
        </div>
    @endif

    @if (!empty($companyAddress))
        <div class="company-meta">
            <strong>Address:</strong> {!! nl2br(e($companyAddress)) !!}
        </div>
    @endif

    <div class="doc-subtitle">Quotation Document</div>
</div>
    <table class="meta-table">
        <tr>
            <td class="label">Quotation No</td>
            <td>{{ $lead->strLeadNo }}</td>
            <td class="label">Date</td>
            <td>{{ now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Measurement Date</td>
            <td>{{ $lead->MeasurementVisitDate ? \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') : '—' }}
            </td>
            <td class="label">Customer Mobile</td>
            <td>{{ $lead->customer->strMobile ?? '—' }}</td>
        </tr>
    </table>

    <div class="party-wrap">
        <table class="party-table">
            <tr>
                <td>
                    <div class="party-title">Customer Details</div>
                    <!-- <div><strong>Name:</strong> {{ $lead->customer->strCustomer ?? '—' }}</div>
                    <div><strong>Address:</strong> {{ $lead->customer->strAddress ?? '—' }}</div>
                    <div><strong>Site Address:</strong> {{ $lead->SiteAddress ?? '—' }}</div> -->
                    @php
                        $companyName = trim((string) ($lead->customer->company_name ?? ''));
                        $contactPersonName = trim((string) ($lead->customer->strCustomer ?? ''));
                        $customerAddress = trim((string) ($lead->customer->strAddress ?? ''));
                        $siteAddress = trim((string) ($lead->SiteAddress ?? ''));
                    @endphp

                    @if ($companyName !== '')
                        <div><strong>Company Name:</strong> {{ $companyName }}</div>
                    @endif
                    @if (!empty($lead->customer->gst_no))
                        <div><strong>GST No:</strong> {{ $lead->customer->gst_no }}</div>
                    @endif
                    <div><strong>Contact Person Name:</strong> {{ $contactPersonName !== '' ? $contactPersonName : '—' }}</div>
                    <div><strong>Address:</strong> {{ $customerAddress !== '' ? $customerAddress : '—' }}</div>
                    @if ($siteAddress !== '')
                        <div><strong>Site Address:</strong> {{ $siteAddress }}</div>
                    @endif
                </td>
                <td>
                    <div class="party-title">Prepared By:-
                        {{ optional($lead->createdBy)->name ?? (optional($lead->createdBy)->first_name ?? 'N/A') }}
                    </div>
                     <div><strong>Sales Person Mobile:</strong> {{ optional($lead->createdBy)->mobile_number ?? optional($lead->createdBy)->strUserMobile ?? '—' }}</div>

                    <div>{{ config('app.name', 'Mirror CRM') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- @if (!$canViewFinancial)
        <div class="notice">
            This quotation includes only product, measurement, and quantity details. Pricing columns are hidden for your access level.
        </div>
    @endif --}}

    <div class="section-title">Quotation Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:12%">Category</th>
                <th style="width:14%">Product</th>
                <th style="width:10%">Shape</th>
                <th style="width:10%">Feature</th>
                <th style="width:7%">Unit</th>
                <th style="width:5%">Qty</th>
                <th style="width:6%">Width</th>
                <th style="width:6%">Height</th>
                @if ($canViewFinancial)

                    <th style="width:10%">Amount</th>
                @endif
                @if ($hasRemarksColumn)
                    <th style="width:12%">Remarks</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($lead->quotations as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->category->strCategoryName ?? '—' }}</td>
                    <td>{{ $item->product->strProductName ?? '—' }}</td>
                    <td>{{ optional($item->shape)->shape_title ?? '—' }}</td>
                    <td>{{ optional($item->feature)->feature_name ?? '—' }}</td>
                    <td>{{ $item->unit_of_measurement ?? '—' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>{{ $item->decWidth }}</td>
                    <td>{{ $item->decHeight }}</td>
                    @if ($canViewFinancial)
                        <td class="text-right">{{ number_format((float) ($item->iAmount ?? 0), 2) }}</td>
                    @endif
                    @if ($hasRemarksColumn)
                        <td>{{ $item->remarks ?? '—' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-terms-table">
        <tr>
            <td class="terms-cell">
                <div class="detail-title">Terms and Conditions</div>
                <div class="detail-body">
                    @if ($termsAndConditions !== '')
                        {!! $termsAndConditions !!}
                    @else
                        <ol>
                            <li>Prices are valid for 7 days from the quotation date.</li>
                            <li>Material dispatch and installation schedule will be confirmed after order confirmation.
                            </li>
                            <li>Any change in dimensions/specifications after approval may revise the final amount.</li>
                            <li>Taxes and additional charges, if applicable, will be charged as per actuals.</li>
                        </ol>
                    @endif

                </div>
                @if ($bankDetails !== '')
                    <div class="detail-title">Bank Details</div>
                    <div class="detail-body">{!! $bankDetails !!}</div>
                @endif
            </td>
            @if ($canViewFinancial)
                <td class="summary-cell">
                    <table class="summary-table">
                        <tr>
                            <td>Total Qty</td>
                            <td class="text-right">{{ number_format($totalQty, 0) }}</td>
                        </tr>
                       
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-right">₹{{ number_format($subtotalAmount, 2) }}</td>
                        </tr>
                        @if ($fittingCharges > 0)
                            <tr>
                                <td>Fitting Charges</td>
                                <td class="text-right">₹{{ number_format($fittingCharges, 2) }}</td>
                            </tr>
                        @endif
                        @if ($deliveryCharges > 0)
                            <tr>
                                <td>Delivery Charges</td>
                                <td class="text-right">₹{{ number_format($deliveryCharges, 2) }}</td>
                            </tr>
                        @endif
                        @if ($packingCharges > 0)
                            <tr>
                                <td>Packing Charges</td>
                                <td class="text-right">₹{{ number_format($packingCharges, 2) }}</td>
                            </tr>
                        @endif
                        @if ($discountAmount > 0)
                            <tr>
                                <td>Discount Amount</td>
                                <td class="text-right">- ₹{{ number_format($discountAmount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Net Amount</td>
                            <td class="text-right">₹{{ number_format($netAmount, 2) }}</td>
                        </tr>
                        @if ((int) ($lead->isGstApplicable ?? 0) === 1)
                            <tr>
                                <td>GST (18%)</td>
                                <td class="text-right">₹{{ number_format($gstAmount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total">
                            <td>Grand Total</td>
                            <td class="text-right">
                                ₹{{ number_format((float) ($lead->iLeadAmount ?? $netAmount + $gstAmount), 2) }}</td>
                        </tr>
                    </table>
                </td>
            @endif
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td class="left-sign">
                <div class="sign-line"></div>
                <div class="sign-text-left">Customer Signature</div>
            </td>
            <td class="right-sign">
                <div class="sign-line"></div>
                <div class="sign-text-right">Authorized Signature</div>
            </td>
        </tr>
    </table>
</body>

</html>
