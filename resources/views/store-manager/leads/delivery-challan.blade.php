<!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
    <title>Delivery Challan {{ $lead->strLeadNo }}</title>
     <style>
        @page {
            margin: 24px;
        }

         body {
             font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #2b2f33;
         }
 
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
            margin-top: 14px;
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
 
         .signatures {

            width: 100%;
             margin-top: 60px;
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
         $logoPath = base_path('assets/images/logo.png');
        $companyGstNo = "GSTIN: 24BIQPG6204F1ZH, State: 24-Gujarat";
        $companyAddress ="10, Sahyog Estate, Behind Anand Restaurant, Isanpur, A’bad 382443
Phone no.: +91 88662 77000 Email: hypermirror01@gmail.com";
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


        <div class="doc-subtitle">Delivery Challan</div>
     </div>
 

    <table class="meta-table">
        <tr>
            <td class="label">Challan No</td>
            <td>DC-{{ $lead->strLeadNo }}</td>
            <td class="label">Date</td>
            <td>{{ now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Lead No</td>
            <td>{{ $lead->strLeadNo }}</td>
            <td class="label">Customer Mobile</td>
            <td>{{ $lead->customer->strMobile ?? '—' }}</td>
        </tr>
    </table>

    <div class="party-wrap">
        <table class="party-table">
            <tr>
                <td>
                    <div class="party-title">Deliver To</div>
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
 
    <div class="section-title">Delivery Items</div>
    <table class="items-table">
         <thead>
             <tr>

                <th style="width:4%">#</th>
                <th style="width:14%">Category</th>
                <th style="width:16%">Product</th>
                <th style="width:12%">Shape</th>
                <th style="width:12%">Feature</th>
                <th style="width:8%">Unit</th>
                <th style="width:8%">Qty</th>
                <th style="width:8%">Width</th>
                <th style="width:8%">Height</th>
                <th style="width:10%">Sqft</th>
             </tr>
         </thead>
         <tbody>
            @forelse ($quotationItems as $index => $item)
                 <tr>
                    <td>{{ $index + 1 }}</td>
                     <td>{{ optional($item->category)->strCategoryName ?? '—' }}</td>
                    <td>{{ optional($item->product)->strProductName ?? '—' }}</td>
                     <td>{{ optional($item->shape)->shape_title ?? '—' }}</td>
                     <td>{{ optional($item->feature)->feature_name ?? '—' }}</td>
                     <td>{{ $item->unit_of_measurement ?? '—' }}</td>

                    <td class="text-right">{{ $item->quantity ?? 1 }}</td>
                    <td class="text-right">{{ $item->decWidth ?? '—' }}</td>
                    <td class="text-right">{{ $item->decHeight ?? '—' }}</td>
                    <td class="text-right">{{ number_format((float) ($item->decTotalSqft ?? 0), 2) }}</td>
                 </tr>
             @empty
                 <tr>
                    <td colspan="10" class="text-right">No items found.</td>
                 </tr>
             @endforelse
            <tr>
                <td colspan="6" class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ $quotationItems->sum('quantity') }}</strong></td>
                 <td></td>
                 <td></td>
                <td class="text-right"><strong>{{ number_format((float) $quotationItems->sum('decTotalSqft'), 2) }}</strong></td>
             </tr>
        </tbody>
     </table>
 

    <div class="detail-title">Note</div>
    <div class="detail-body">
        This is a computer-generated delivery challan for internal dispatch and customer handover reference.
     </div>
 

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
