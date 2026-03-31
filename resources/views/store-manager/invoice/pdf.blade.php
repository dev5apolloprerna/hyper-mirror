<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->strInvoiceNo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 12px; }
        .header { margin-bottom: 18px; }
        .title { font-size: 20px; font-weight: 700; margin: 0; }
        .muted { color: #64748b; margin: 2px 0; }
        .meta-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .meta-table td { padding: 4px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.items th, table.items td { border: 1px solid #cbd5e1; padding: 8px; }
        table.items th { background: #f1f5f9; text-align: left; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .total-row td { font-weight: 700; background: #f8fafc; }
        .notes { margin-top: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Invoice {{ $invoice->strInvoiceNo }}</p>
        <p class="muted">Date: {{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d F Y') }}</p>
        <table class="meta-table">
            <tr>
                <td><strong>Showroom:</strong> {{ optional($invoice->showroom)->strShowRoomName ?? '—' }}</td>
                <td><strong>Created By:</strong> {{ optional($invoice->createdBy)->strUserName ?: optional($invoice->createdBy)->first_name ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong> {{ strtoupper($invoice->status ?? 'draft') }}</td>
                <td><strong>Total:</strong> ₹{{ number_format((float) $invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th>Category</th>
                <th>Product</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-end" style="width: 18%;">Unit Price</th>
                <th class="text-end" style="width: 18%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ optional($item->category)->strCategoryName ?? '—' }}</td>
                    <td>{{ optional($item->product)->strProductName ?? '—' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-end">₹{{ number_format((float) $item->iAmount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">TOTAL</td>
                <td class="text-end">₹{{ number_format((float) $invoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->strNotes)
        <div class="notes">
            <strong>Notes:</strong> {{ $invoice->strNotes }}
        </div>
    @endif
</body>
</html>
