 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <title>Invoice {{ $invoice->strInvoiceNo }}</title>
     <style>
         body {
             font-family: DejaVu Sans, sans-serif;
             color: #0f172a;
             font-size: 11px;
             /*margin: 26px;*/
         }
		 .header-wrap {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #d1d5db;
    }
	.doc-subtitle {
        font-size: 12px;
        margin-top: 6px;
        text-align: center;
        font-weight: 700;
    }
    .header-logo {
        width: 150px;
        height: auto;
        display: block;
        margin: 0 auto 6px;
    }
         .heading {
             font-size: 38px;
             font-weight: 800;
             color: #153e75;
             margin: 0 0 2px 0;
             letter-spacing: .4px;
         }
         .header {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 8px;
         }

         .header td {
             vertical-align: top;
         }

         .header-right {
             text-align: right;
         }

         .company-logo {
             max-width: 150px;
             max-height: 70px;
         }

         .invoice-no {
             margin: 0 0 14px 0;
             color: #334155;
         }

         .box {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 14px;
         }

         .box td {
             border: 1px solid #cbd5e1;
             padding: 8px 10px;
             vertical-align: top;
         }

         .meta td {
             width: 50%;
         }

        .meta-row {
             margin-bottom: 3px;
         }

         .meta-row:last-child {
             margin-bottom: 0;
         }
                  .meta-value {
             display: inline-block;
             vertical-align: top;
         }

         .meta-title {
             display: inline-block;
             min-width: 118px;
             font-weight: 700;
             color: #0f172a;
         }

         .split td {
             width: 50%;
         }

         .section-title {
             font-size: 13px;
             font-weight: 700;
             margin: 0 0 3px 0;
             color: #0f172a;
         }

         .muted {
             color: #475569;
             line-height: 1.5;
         }

         .items {
             width: 100%;
             border-collapse: collapse;
             margin-top: 8px;
         }

         .items th,
         .items td {
             border: 1px solid #cbd5e1;
             padding: 8px;
         }

         .items th {
             background: #e2e8f0;
             color: #0f172a;
             text-align: left;
         }

         .center {
             text-align: center;
         }

         .right {
             text-align: right;
         }

         .summary {
             width: 40%;
             margin-left: auto;
             border-collapse: collapse;
             margin-top: 10px;
         }

         .summary td {
             border: 1px solid #cbd5e1;
             padding: 7px 9px;
         }

         .summary .lbl {
             font-weight: 700;
             text-align: right;
         }

         .summary .grand td {
             font-weight: 800;
             background: #f1f5f9;
         }
         .notes {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .notes td {
            padding: 0;
            vertical-align: top;
        }


         .sign {
             margin-top: 28px;
             width: 100%;
         }

         .sign .line {
             width: 220px;
             border-top: 1px solid #334155;
             margin-left: auto;
             padding-top: 6px;
             text-align: center;
             font-weight: 700;
         }
        .detail-section { margin-top: 14px; }
        .section-title { background: #e2e8f0; padding: 6px 8px; font-weight: 700; }
        .section-body { border: 1px solid #cbd5e1; border-top: 0; padding: 8px; white-space: pre-line; }
     </style>
 </head>

 <body>


         @php
		  $companyGstNo = "GSTIN: 24BIQPG6204F1ZH, State: 24-Gujarat";
        $defaultAddress = "10, Sahyog Estate, Behind Anand Restaurant, Isanpur, A’bad 382443";
        $salesPersonAddress = trim((string) (optional($invoice->createdBy)->strUserAddress ?? ''));
        $salesPersonMobile = trim((string) (optional($invoice->createdBy)->mobile_number ?? optional($invoice->createdBy)->strUserMobile ?? ''));

        $companyAddressLines = [
            $salesPersonAddress !== '' ? $salesPersonAddress : $defaultAddress,
        ];

        if ($salesPersonMobile !== '') {
            $companyAddressLines[] = 'Phone no.: +91 ' . $salesPersonMobile;
        }

        $companyAddress = implode("\n", $companyAddressLines);

        $logoPath = base_path('assets/images/logo.png');
        $logoSrc = file_exists($logoPath) ? $logoPath : null;
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

    <div class="doc-subtitle">Invoice</div>
</div>
     <table class="box meta">
         <tr>
             <td>
                 <div class="meta-row"><span class="meta-title">Invoice Date</span><span class="meta-value">{{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d F Y') }}</span></div>
                 <div class="meta-row"><span class="meta-title">Showroom</span><span class="meta-value">{{ optional($invoice->showroom)->strShowRoomName ?? '—' }}</span></div>
                 <div class="meta-row"><span class="meta-title">Payment Mode</span><span class="meta-value">{{ ucfirst($invoice->payment_mode ?? 'cash') }}</span></div>
                 <div class="meta-row"><span class="meta-title">Status</span><span class="meta-value">{{ strtoupper($invoice->status ?? 'draft') }}</span></div>
             </td>
             <td>
                 <div class="meta-row"><span class="meta-title">Created By</span><span class="meta-value">{{ optional($invoice->createdBy)->strUserName ?: optional($invoice->createdBy)->first_name ?? '—' }}</span></div>
                 <div class="meta-row"><span class="meta-title">Payment Received</span><span class="meta-value">{{ $invoice->payment_received ? 'Yes' : 'No' }}</span></div>
                 <div class="meta-row"><span class="meta-title">Subtotal</span><span class="meta-value">₹{{ number_format((float) $invoice->total_amount, 2) }}</span></div>
                 <div class="meta-row"><span class="meta-title">Total</span><span class="meta-value">₹{{ number_format((float) $invoice->total_amount, 2) }}</span></div>
             </td>
         </tr>
     </table>

     <table class="box split">
         <tr>
             <td>
                 <p class="section-title">Bill To</p>
                 <div class="muted">{{ $invoice->customer_name ?: 'Customer Name' }}</div>
                 <div class="muted">{{ $invoice->customer_mobile ?: 'Mobile Number' }}</div>
                 <div class="muted">{{ $invoice->customer_address ?: 'Address' }}</div>
             </td>
             <!-- <td>
                 <p class="section-title">Business Details</p>
                 <div class="muted">{{ optional($invoice->showroom)->strShowRoomName ?? 'Company / Showroom' }}</div>
                 <div class="muted">Invoice generated by Store Manager</div>
             </td> -->
         </tr>
     </table>

     <table class="items">
         <thead>
             <tr>

                 <th style="width: 5%;">#</th>
                 <th style="width: 17%;">Category</th>
                 <th>Product</th>

                 <th class="center" style="width: 8%;">Qty</th>
                 <th style="width: 18%;">Remark</th>

                 <th class="right" style="width: 16%;">Unit Price</th>
                 <th class="right" style="width: 16%;">Amount</th>
             </tr>
         </thead>
         <tbody>
             @foreach ($invoice->items as $i => $item)
                 <tr>
                     <td>{{ $i + 1 }}</td>
                     <td>{{ optional($item->category)->strCategoryName ?? '—' }}</td>
                     <td>{{ optional($item->product)->strProductName ?? '—' }}</td>

                     <td class="center">{{ $item->quantity }}</td>
                     <td>{{ $item->item_remark ?: '—' }}</td>

                     <td class="right">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                     <td class="right">₹{{ number_format((float) $item->iAmount, 2) }}</td>
                 </tr>
             @endforeach
         </tbody>

     </table>


     <table class="notes">
    <tr>
        <td style="width: 55%; vertical-align: top; padding: 0; border: 0;">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="background:#6b7280; color:#fff; font-weight:700; padding:6px 10px; border:1px solid #cbd5e1;">
                        Invoice Amount In Words
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #cbd5e1; padding:8px 10px;">
                        {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format((float) $invoice->total_amount) }}
                        Rupees only
                    </td>
                </tr>
                <tr>
                    <td style="background:#6b7280; color:#fff; font-weight:700; padding:6px 10px; border:1px solid #cbd5e1; border-top:0;">
                        Notes
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #cbd5e1; border-top:0; padding:8px 10px;">
                        {{ $invoice->strNotes ?: '—' }}
                    </td>
                </tr>
            </table>
        </td>

        <td style="width: 3%; border:0;"></td>

        <td style="width: 42%; vertical-align: top; padding: 0; border: 0;">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td colspan="2" style="background:#6b7280; color:#fff; font-weight:700; padding:6px 10px; border:1px solid #cbd5e1;">
                        Amounts
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #cbd5e1; padding:8px 10px;">Sub Total</td>
                    <td style="border:1px solid #cbd5e1; padding:8px 10px; text-align:right;">
                        ₹{{ number_format((float) $invoice->total_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #cbd5e1; padding:8px 10px; font-weight:700;">Total</td>
                    <td style="border:1px solid #cbd5e1; padding:8px 10px; text-align:right; font-weight:700;">
                        ₹{{ number_format((float) $invoice->total_amount, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
    @if(!empty(optional($invoicePdfSetting)->terms_and_conditions))
        <div class="detail-section">
            <div class="section-title">Terms and Conditions</div>
            <div class="section-body">{!! $invoicePdfSetting->terms_and_conditions !!}</div>
        </div>
    @endif
    @if(!empty(optional($invoicePdfSetting)->bank_details))
        <div class="detail-section">
            <div class="section-title">Bank Details</div>
            <div class="section-body">{!! $invoicePdfSetting->bank_details !!}</div>
        </div>
    @endif

     <div class="sign">
         <div class="line">Authorized Signature</div>
     </div>
 </body>

 </html>
