    @extends('layouts.app')
    @section('title', 'Invoices')

    @section('styles')
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 18px 22px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
        }

        .stat-card .lbl {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
        }

        .stat-card .val {
            font-size: 26px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .filter-bar {
            background: #fff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        }

        .inv-table thead th {
            background: #1e293b;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            border-color: #334155;
            vertical-align: middle;
        }

        .inv-table tbody tr:hover {
            background: #f8faff;
        }

        .inv-table tbody td {
            font-size: 13px;
            vertical-align: middle;
        }

        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
    </style>
    @endsection

    @section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @include('common.alert')

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0 fw-bold">Invoices</h4>
                        <p class="text-muted mb-0 mt-1 small">Create and manage product invoices.</p>
                    </div>
                    <a href="{{ route('store.invoice.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Invoice
                    </a>
                </div>

                {{-- Summary Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Total Invoices</div>
                            <div class="val">{{ number_format($totalInvoices) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Grand Total</div>
                            <div class="val text-success">₹{{ number_format($grandTotal, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Today Cash</div>
                            <div class="val text-primary">₹{{ number_format($todayCashAmount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Today Bank</div>
                            <div class="val text-info">₹{{ number_format($todayBankAmount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Today Unpaid</div>
                            <div class="val text-warning">₹{{ number_format($todayUnpaidAmount, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card">
                            <div class="lbl">Overall Unpaid</div>
                            <div class="val text-danger">₹{{ number_format($overallUnpaid, 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="filter-bar mb-4">
                    <form method="GET" action="{{ route('store.invoice.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control form-control-sm" placeholder="Invoice / branch / paid">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Showroom</label>
                                <select name="iShowroomId" class="form-select form-select-sm">
                                    <option value="">All Showrooms</option>
                                    @foreach($showrooms as $s)
                                    <option value="{{ $s->iShowroomId }}" {{ request('iShowroomId') == $s->iShowroomId ? 'selected' : '' }}>
                                        {{ $s->strShowRoomName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Paid Status</label>
                                <select name="payment_status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">From Date</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">To Date</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-success btn-sm flex-fill">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                                <a href="{{ route('store.invoice.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Invoice List</h6>
                        <small class="text-muted">{{ $invoices->total() }} record(s)</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table inv-table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Showroom</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Unpaid Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Payment Received</th>
                                        <th>Comments</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $inv)
                                    <tr>
                                        <td>{{ $invoices->firstItem() + $loop->index }}</td>
                                        <td>
                                            <a href="{{ route('store.invoice.show', $inv->iInvoiceId) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $inv->strInvoiceNo }}
                                            </a>
                                        </td>
                                        <td class="text-nowrap">
                                            {{ \Carbon\Carbon::parse($inv->InvoiceDate)->format('d-m-Y') }}
                                        </td>
                                        <td>{{ optional($inv->showroom)->strShowRoomName ?? '—' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-dark">{{ $inv->items->count() }}</span>
                                        </td>
                                        <td class="fw-bold">
                                            ₹{{ number_format($inv->total_amount, 2) }}
                                        </td>
                                        <td class="fw-bold text-warning">
                                            ₹{{ number_format($inv->unpaid_amount, 2) }}
                                        </td>
                                        <td>
                                            @if($inv->payment_received)
                                            <span class="badge bg-secondary"> {{ ucfirst($inv->payment_mode ?? 'cash') }} </span>
                                            @else
                                            <form method="POST" action="{{ route('store.invoice.update-payment', $inv->iInvoiceId) }}" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="payment_received" value="{{ $inv->payment_received ? 1 : 0 }}">
                                                <input type="hidden" name="strNotes" value="{{ $inv->strNotes }}">
                                                <select name="payment_mode" class="form-select form-select-sm">
                                                    <option value="cash" {{ $inv->payment_mode === 'cash' ? 'selected' : '' }}>Cash</option>
                                                    <option value="bank" {{ $inv->payment_mode === 'bank' ? 'selected' : '' }}>Bank</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Update payment mode">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->payment_received)
                                            <span class="badge bg-success">Yes</span>
                                            @else
                                            <form method="POST" action="{{ route('store.invoice.update-payment', $inv->iInvoiceId) }}" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="payment_mode" value="{{ $inv->payment_mode ?? 'cash' }}">
                                                <input type="hidden" name="strNotes" value="{{ $inv->strNotes }}">
                                                <select name="payment_received" class="form-select form-select-sm">
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Update payment received">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->payment_received)
                                            {{ $inv->strNotes ?: '—' }}
                                            @else
                                            <form method="POST" action="{{ route('store.invoice.update-payment', $inv->iInvoiceId) }}" class="d-flex align-items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="payment_mode" value="{{ $inv->payment_mode ?? 'cash' }}">
                                                <input type="hidden" name="payment_received" value="0">
                                                <input type="text" name="strNotes" class="form-control form-control-sm" value="{{ $inv->strNotes }}" placeholder="Comment required">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Save comment">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->status === 'confirmed')
                                            <span class="badge-status" style="background:#d1e7dd;color:#0f5132;">Confirmed</span>
                                            @elseif($inv->status === 'draft')
                                            <span class="badge-status" style="background:#fff3cd;color:#664d03;">Draft</span>
                                            @else
                                            <span class="badge-status" style="background:#f8d7da;color:#842029;">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($inv->createdBy)->strUserName ?: optional($inv->createdBy)->first_name ?? '—' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('store.invoice.show', $inv->iInvoiceId) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- <form action="{{ route('store.invoice.destroy', $inv->iInvoiceId) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this invoice?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>
                                            No invoices found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($invoices->count())
                                <tfoot>
                                    <tr class="table-success fw-bold">
                                        <td colspan="5" class="text-end">Grand Total (filtered)</td>
                                        <td>₹{{ number_format($grandTotal, 2) }}</td>
                                        <td colspan="7"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        <div class="p-3">{{ $invoices->links() }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection