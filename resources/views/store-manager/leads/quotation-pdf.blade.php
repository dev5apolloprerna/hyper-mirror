<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation — {{ $lead->strLeadNo }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; padding: 30px; }

        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #333; padding-bottom: 14px; }
        .header h1 { font-size: 22px; font-weight: 700; color: #1a1a2e; }
        .header p  { font-size: 12px; color: #666; }

        .section-title { font-size: 14px; font-weight: 700; color: #1a1a2e;
                         border-bottom: 1px solid #ccc; padding-bottom: 6px; margin: 20px 0 10px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 30px; margin-bottom: 16px; }
        .info-row  { display: flex; gap: 8px; }
        .info-label { font-weight: 600; min-width: 140px; color: #555; }
        .info-value { color: #222; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 7px 10px; text-align: left; }
        th { background: #1a1a2e; color: #fff; font-size: 12px; }
        tr:nth-child(even) td { background: #f7f7f7; }

        tfoot th { background: #f0f0f0; color: #222; }
        tfoot tr:last-child th { background: #1a1a2e; color: #fff; font-size: 14px; }

        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .signature-box { text-align: center; }
        .signature-box p { font-weight: 600; margin-bottom: 40px; }
        .signature-line { border-top: 1px solid #333; width: 180px; margin: 0 auto; }

        .notice { background: #fff8e1; border: 1px solid #ffc107; border-radius: 6px;
                  padding: 10px 14px; margin-bottom: 16px; font-size: 12px; color: #664d03; }

        @media print {
            body { padding: 10px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

        @php
        $hasRemarksColumn = $lead->quotations->contains(function ($quotationItem) {
            return !empty($quotationItem->remarks);
        });

        $subtotalAmount = (float) $lead->quotations->sum('iAmount');
        $fittingCharges = (float) ($lead->iFittingCharges ?? 0);
        $discountAmount = (int) ($lead->isDiscountApplicable ?? 0) === 1 ? (float) ($lead->decDiscountAmount ?? 0) : 0;
        $amountAfterDiscount = max(($subtotalAmount + $fittingCharges) - $discountAmount, 0);
        $gstAmount = (int) ($lead->isGstApplicable ?? 0) === 1 ? (float) ($lead->decGstAmount ?? ($amountAfterDiscount * 0.18)) : 0;
        $summaryColspan = $hasRemarksColumn ? 12 : 11;
    @endphp


    <div class="no-print" style="margin-bottom:16px; text-align:right;">
        <button onclick="window.print()" style="padding:8px 18px; background:#1a1a2e; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px;">
            🖨 Print / Save PDF
        </button>
        <button onclick="window.close()" style="padding:8px 18px; background:#888; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px; margin-left:8px;">
            ✕ Close
        </button>
    </div>

    <div class="header">
        <h1>{{ config('app.name', 'Mirror CRM') }}</h1>
        <p>Quotation — Lead No: <strong>{{ $lead->strLeadNo }}</strong></p>
        <p>Date: {{ now()->format('d-m-Y') }}</p>
    </div>

    @if(!$canViewFinancial)
        <div class="notice">
            ℹ This quotation shows product and dimension details only. Pricing information is not included.
        </div>
    @endif

    <div class="section-title">Customer Details</div>
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Customer Name</span><span class="info-value">{{ $lead->customer->strCustomer ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Mobile</span><span class="info-value">{{ $lead->customer->strMobile ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Address</span><span class="info-value">{{ $lead->customer->strAddress ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Site Address</span><span class="info-value">{{ $lead->SiteAddress ?? '—' }}</span></div>
    </div>

    <div class="section-title">Lead Details</div>
    <div class="info-grid">
        <div class="info-row"><span class="info-label">Lead No</span><span class="info-value">{{ $lead->strLeadNo }}</span></div>
        <div class="info-row"><span class="info-label">Status</span><span class="info-value">{{ $lead->iCurrentLeadStatus }}</span></div>
        <div class="info-row"><span class="info-label">Measurement Required</span><span class="info-value">{{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</span></div>
        @if($lead->MeasurementVisitDate)
        <div class="info-row"><span class="info-label">Measurement Date</span><span class="info-value">{{ \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') }}</span></div>
        @endif
    </div>

    <div class="section-title">Quotation Items</div>
    <table>
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
                    <th>Sqft</th>
                    <th>Amount (₹)</th>
                @endif
                @if($hasRemarksColumn)
                    <th>Remarks</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($lead->quotations as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->category->strCategoryName ?? '—' }}</td>
                    <td>{{ $item->product->strProductName ?? '—' }}</td>
                    <td>{{ optional($item->shape)->shape_title ?? '—' }}</td>
                    <td>{{ optional($item->feature)->feature_name ?? '—' }}</td>
                    <td>{{ $item->unit_of_measurement ?? '—' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>{{ $item->decHeight }}</td>
                    <td>{{ $item->decWidth }}</td>
                    @if($canViewFinancial)
                        <td>{{ number_format((float)($item->decRatePerSqft ?? 0), 2) }}</td>
                        <td>{{ number_format((float)($item->decTotalSqft ?? 0), 2) }}</td>
                        <td>{{ number_format((float)($item->iAmount ?? 0), 2) }}</td>
                    @endif
                    @if($hasRemarksColumn)
                        <td>{{ $item->remarks ?? '' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        @if($canViewFinancial)
        <tfoot>
            <tr>
            <th colspan="{{ $summaryColspan }}" style="text-align:right;">Subtotal</th>
                <th>₹{{ number_format($subtotalAmount, 2) }}</th>
            </tr>
            @if($fittingCharges > 0)
            <tr>
                <th colspan="{{ $summaryColspan }}" style="text-align:right;">Fitting Charges</th>
                <th>₹{{ number_format($fittingCharges, 2) }}</th>
            </tr>
            @endif
            @if((int) ($lead->isDiscountApplicable ?? 0) === 1)
            <tr>
                <th colspan="{{ $summaryColspan }}" style="text-align:right;">Discount</th>
                <th>- ₹{{ number_format($discountAmount, 2) }}</th>
            </tr>
            @endif
            @if((int) ($lead->isGstApplicable ?? 0) === 1)
            </tr>
            @if((float)($lead->iFittingCharges ?? 0) > 0)
            <tr>
                <th colspan="{{ $summaryColspan }}" style="text-align:right;">GST (18%)</th>
                <th>₹{{ number_format($gstAmount, 2) }}</th>
            </tr>
            @endif
            <tr>
                <th colspan="{{ $summaryColspan }}" style="text-align:right;">Grand Total</th>
                <th>₹{{ number_format((float)($lead->iLeadAmount ?? ($amountAfterDiscount + $gstAmount)), 2) }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Customer Signature</p>
            <div class="signature-line"></div>
            <p style="margin-top:6px; font-size:12px;">{{ $lead->customer->strCustomer ?? '' }}</p>
        </div>
        <div class="signature-box">
            <p>Authorized Signature</p>
            <div class="signature-line"></div>
            <p style="margin-top:6px; font-size:12px;">{{ config('app.name') }}</p>
        </div>
    </div>

</body>
</html>
