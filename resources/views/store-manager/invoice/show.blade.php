@extends('layouts.app')
@section('title', 'Invoice — ' . $invoice->strInvoiceNo)

@section('styles')
    <style>
        .inv-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, .08);
        }

        .inv-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            border-radius: 14px 14px 0 0;
            padding: 28px 32px;
            color: #fff;
        }

        .inv-header h3 {
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .inv-header .meta {
            opacity: .7;
            font-size: 13px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 30px;
            padding: 22px 28px;
        }

        .info-grid .lbl {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }

        .info-grid .val {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .items-table thead th {
            background: #f1f5f9;
            color: #1e293b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 2px solid #e2e8f0;
        }

        .items-table tbody td {
            font-size: 13px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .items-table tfoot td {
            background: #1e293b;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .inv-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Actions --}}
                <div class="d-flex gap-2 mb-4 no-print flex-wrap">
                    <a href="{{ route('store.invoice.index') }}" class="btn btn-light border">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <a href="{{ route('store.invoice.pdf', $invoice->iInvoiceId) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> View PDF
                    </a>
                    @php($roleSlug = optional(auth()->user()->crmRole)->slug)
                    @if (blank($roleSlug) || $roleSlug === 'admin')
                        <form action="{{ route('store.invoice.destroy', $invoice->iInvoiceId) }}" method="POST"
                            onsubmit="return confirm('Delete this invoice?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>

                <div class="card inv-card">

                    {{-- Invoice Header --}}
                    <div class="inv-header">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <p class="meta mb-1">INVOICE</p>
                                <h3 class="mb-1">{{ $invoice->strInvoiceNo }}</h3>
                                <p class="meta mb-0">
                                    Date: {{ \Carbon\Carbon::parse($invoice->InvoiceDate)->format('d F Y') }}
                                </p>
                            </div>
                            <div class="text-end">
                                @if ($invoice->status === 'confirmed')
                                    <span
                                        style="background:rgba(34,197,94,.2);color:#86efac;padding:6px 18px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;">
                                        ✓ CONFIRMED
                                    </span>
                                @elseif($invoice->status === 'cancelled')
                                    <span
                                        style="background:rgba(239,68,68,.2);color:#fca5a5;padding:6px 18px;border-radius:20px;font-size:12px;font-weight:700;">
                                        ✗ CANCELLED
                                    </span>
                                @else
                                    <span
                                        style="background:rgba(234,179,8,.2);color:#fde047;padding:6px 18px;border-radius:20px;font-size:12px;font-weight:700;">
                                        DRAFT
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Meta info --}}
                    <div class="info-grid border-bottom">
                        <div>
                            <div class="lbl">Showroom</div>
                            <div class="val">{{ optional($invoice->showroom)->strShowRoomName ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="lbl">Created By</div>
                            <div class="val">
                                {{ optional($invoice->createdBy)->strUserName ?: optional($invoice->createdBy)->first_name ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <div class="lbl">Payment Mode</div>
                            <div class="val">{{ ucfirst($invoice->payment_mode ?? 'cash') }}</div>
                        </div>
                        <div>
                            <div class="lbl">Payment Received</div>
                            <div class="val">{{ $invoice->payment_received ? 'Yes' : 'No' }}</div>
                        </div>
                        <div>
                            <div class="lbl">Customer Name</div>
                            <div class="val">{{ $invoice->customer_name ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="lbl">Customer Mobile</div>
                            <div class="val">{{ $invoice->customer_mobile ?: '—' }}</div>
                        </div>
                        <div style="grid-column:span 2;">
                            <div class="lbl">Customer Address</div>
                            <div class="val">{{ $invoice->customer_address ?: '—' }}</div>
                        </div>
                        @if ($invoice->strNotes)
                            <div style="grid-column:span 2;">
                                <div class="lbl">Notes</div>
                                <div class="val">{{ $invoice->strNotes }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div class="p-3 p-md-4">
                        <div class="table-responsive">
                            <table class="table items-table mb-0">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th>Remark</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ optional($item->category)->strCategoryName ?? '—' }}</td>
                                            <td>{{ optional($item->product)->strProductName ?? '—' }}</td>
                                            <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                            <td>{{ $item->item_remark ?: '—' }}</td>
                                            <td class="text-end">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                                            <td class="text-end fw-bold">₹{{ number_format((float) $item->iAmount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end pe-4">TOTAL</td>
                                        <td class="text-end pe-3">₹{{ number_format($invoice->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 pb-4 d-flex justify-content-between flex-wrap gap-4 pt-2 border-top">
                        <div style="min-width:200px;">
                            <p class="small text-muted mb-1 fw-semibold">Authorised Signature</p>
                            <div style="border-top:1px solid #334155;width:180px;margin-top:40px;"></div>
                        </div>
                        <div style="min-width:200px;text-align:right;">
                            <p class="small text-muted mb-1 fw-semibold">Received By</p>
                            <div style="border-top:1px solid #334155;width:180px;margin-top:40px;margin-left:auto;"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection