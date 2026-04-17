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
            color: #222;
        }

        .company-title {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 4px;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .meta-table,
        .party-table,
        .items-table,
        .summary-table {
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

        .summary-wrap {
            margin-top: 10px;
            width: 42%;
            margin-left: auto;
        }

        .summary-table td {
            border: 1px solid #cfcfcf;
            padding: 6px 8px;
        }

        .summary-table .total td {
            font-weight: 700;
            background: #f5f5f5;
        }

        /* .notice {
            margin-top: 12px;
            padding: 8px;
            border: 1px solid #facc15;
            background: #fffbeb;
            font-size: 10px;
        } */

        .signatures {
            margin-top: 42px;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 40px;
        }

        .sign-line {
            border-top: 1px solid #444;
            width: 180px;
            margin: 0 auto 5px;
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
    </style>
</head>

<body>
    @php
        $hasRemarksColumn = $lead->quotations->pluck('remarks')->filter()->isNotEmpty();

        $subtotalAmount = (float) $lead->quotations->sum('iAmount');
        $fittingCharges = (float) ($lead->iFittingCharges ?? 0);
        $discountAmount = (int) ($lead->isDiscountApplicable ?? 0) === 1 ? (float) ($lead->decDiscountAmount ?? 0) : 0;
        $amountAfterDiscount = max($subtotalAmount + $fittingCharges - $discountAmount, 0);
        $gstAmount =
            (int) ($lead->isGstApplicable ?? 0) === 1
                ? (float) ($lead->decGstAmount ?? $amountAfterDiscount * 0.18)
                : 0;
        $netAmount = $amountAfterDiscount;
    @endphp

    <div class="company-title">{{ config('app.name', 'Mirror CRM') }}</div>
    <div class="doc-subtitle">Quotation Document</div>

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
                    <div><strong>Name:</strong> {{ $lead->customer->strCustomer ?? '—' }}</div>
                    <div><strong>Address:</strong> {{ $lead->customer->strAddress ?? '—' }}</div>
                    <div><strong>Site Address:</strong> {{ $lead->SiteAddress ?? '—' }}</div>
                </td>
                <td>
                    <div class="party-title">Prepared By</div>
                    <div>{{ config('app.name', 'Mirror CRM') }}</div>
                    <div>Generated from CRM</div>
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
                    <th style="width:8%">Rate/Sqft</th>
                    <th style="width:8%">Sqft</th>
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
                        <td class="text-right">{{ number_format((float) ($item->decRatePerSqft ?? 0), 2) }}</td>
                        <td class="text-right">{{ number_format((float) ($item->decTotalSqft ?? 0), 2) }}</td>
                        <td class="text-right">{{ number_format((float) ($item->iAmount ?? 0), 2) }}</td>
                    @endif
                    @if ($hasRemarksColumn)
                        <td>{{ $item->remarks ?? '—' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($canViewFinancial)
        <div class="summary-wrap">
            <table class="summary-table">
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
                @if ((int) ($lead->isDiscountApplicable ?? 0) === 1 && $discountAmount > 0)
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
        </div>
    @endif

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
