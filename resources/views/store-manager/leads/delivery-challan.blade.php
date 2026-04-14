<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan — {{ $lead->strLeadNo }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            padding: 30px;
        }

        .dc-header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }
        .dc-header h1 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .dc-header .challan-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 4px;
        }
        .dc-header .meta { font-size: 11px; color: #64748b; }

        .info-section {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .info-block { flex: 1; }
        .info-block h4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .info-block p {
            margin-bottom: 5px;
            font-size: 13px;
            line-height: 1.5;
        }
        .info-block p strong { color: #1e293b; }

        /* Items table */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.items thead th {
            background: #1e293b;
            color: #fff;
            padding: 9px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
        }
        table.items thead th.center { text-align: center; }
        table.items tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        table.items tbody tr:nth-child(even) td { background: #f8fafc; }
        table.items tbody td.center { text-align: center; font-weight: 600; }

        .item-count-row td {
            font-weight: 700;
            background: #f1f5f9 !important;
            padding: 8px 12px;
            font-size: 13px;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .sig-box { text-align: center; }
        .sig-box .sig-label { font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 35px; }
        .sig-box .sig-line { border-top: 1px solid #334155; width: 180px; margin: 0 auto 4px; }
        .sig-box .sig-name { font-size: 11px; color: #64748b; }

        /* Footer */
        .dc-footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }

        /* No print for buttons */
        .no-print { margin-bottom: 16px; text-align: right; }

        @media print {
            body { padding: 10px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()"
                style="padding:8px 20px; background:#1e293b; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px; margin-right:8px;">
            🖨 Print / Save PDF
        </button>
        <button onclick="window.close()"
                style="padding:8px 16px; background:#94a3b8; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px;">
            ✕ Close
        </button>
    </div>

    {{-- Header --}}
    <div class="dc-header">
        <h1>{{ config('app.name', 'Mirror CRM') }}</h1>
        <div class="challan-title">Delivery Challan</div>
        <p class="meta">Challan No: <strong>DC-{{ $lead->strLeadNo }}</strong> &nbsp;|&nbsp; Date: <strong>{{ now()->format('d-m-Y') }}</strong></p>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
        <div class="info-block">
            <h4>Deliver To</h4>
            <p><strong>{{ $lead->customer->strCustomer ?? '—' }}</strong></p>
            <p>📞 {{ $lead->customer->strMobile ?? '—' }}</p>
            @if($lead->customer->strAddress ?? false)
                <p>{{ $lead->customer->strAddress }}</p>
            @endif
            @if($lead->SiteAddress ?? false)
                <p><strong>Site:</strong> {{ $lead->SiteAddress }}</p>
            @endif
        </div>

        <div class="info-block">
            <h4>Challan Details</h4>
            <p><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
            <p><strong>Challan Date:</strong> {{ now()->format('d-m-Y') }}</p>
            
        </div>
    </div>

    {{-- Items Table — only product name + quantity --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:8%;">#</th>
                <th>Product Name</th>
                <th style="width:18%;" class="center">Quantity</th>
                <th style="width:20%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotationItems as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td><strong>{{ optional($item->product)->strProductName ?? '—' }}</strong></td>
                    <td class="center">{{ $item->quantity ?? 1 }}</td>
                    <td>{{ $item->remarks ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px; color:#94a3b8;">
                        No items found.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="item-count-row">
                <td colspan="2" style="text-align:right;">Total Items:</td>
                <td class="center">{{ $quotationItems->sum('quantity') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-label">Received By (Customer)</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $lead->customer->strCustomer ?? '' }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Delivered By</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ config('app.name') }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Authorised Signatory</div>
            <div class="sig-line"></div>
            <div class="sig-name">&nbsp;</div>
        </div>
    </div>

    <div class="dc-footer">
        This is a computer-generated delivery challan. &nbsp;|&nbsp; {{ config('app.name') }} &nbsp;|&nbsp; {{ now()->format('d-m-Y H:i A') }}
    </div>

</body>
</html>