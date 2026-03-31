@extends('layouts.app')

@section('title', 'Business Report')

@section('styles')
<style>
    .metric-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        transition: transform .15s;
    }
    .metric-card:hover { transform: translateY(-2px); }
    .metric-card small {
        color: #64748b;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .metric-card h5 {
        margin-top: 6px;
        margin-bottom: 0;
        font-weight: 700;
        font-size: 22px;
    }
    .table thead th {
        white-space: nowrap;
    }
    .month-tab {
        cursor: pointer;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        display: inline-block;
        margin: 2px;
        transition: all .15s;
    }
    .month-tab:hover, .month-tab.active {
        background: #1e293b;
        color: #fff;
        border-color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Business Report</h4>
                    <p class="text-muted mb-0 mt-1">Track quotation, approved business, collections and pending amounts.</p>
                </div>
            </div>

            {{-- ── Filter Card ── --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex gap-2 align-items-end">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Search</button>
                            <a href="{{ route('admin.reports.business') }}" class="btn btn-light border">Reset</a>
                        </div>
                        {{-- Quick month shortcuts --}}
                        <div class="col-md-5">
                            <label class="form-label d-block">Quick Month</label>
                            @php
                                $months = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $months[$m] = \Carbon\Carbon::create(now()->year, $m, 1);
                                }
                            @endphp
                            @foreach($months as $mo => $monthDate)
                                @php
                                    $mFrom = $monthDate->format('Y-m-d');
                                    $mTo   = $monthDate->copy()->endOfMonth()->format('Y-m-d');
                                    $isActive = $fromDate === $mFrom && $toDate === $mTo;
                                @endphp
                                <a href="{{ route('admin.reports.business', ['from_date' => $mFrom, 'to_date' => $mTo]) }}"
                                   class="month-tab {{ $isActive ? 'active' : '' }}">
                                    {{ $monthDate->format('M') }}
                                </a>
                            @endforeach
                            {{-- Current month shortcut --}}
                            @php
                                $thisMonthFrom = now()->startOfMonth()->format('Y-m-d');
                                $thisMonthTo   = now()->endOfMonth()->format('Y-m-d');
                            @endphp
                            <a href="{{ route('admin.reports.business', ['from_date' => $thisMonthFrom, 'to_date' => $thisMonthTo]) }}"
                               class="month-tab {{ ($fromDate === $thisMonthFrom && $toDate === $thisMonthTo) ? 'active' : '' }}"
                               style="background:#0d6efd;color:#fff;border-color:#0d6efd;">
                                This Month
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Metric Cards ── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Total Quotation Value</small>
                            <h5>₹{{ number_format($totalQuotationValue, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Approved Business</small>
                            <h5 class="text-primary">₹{{ number_format($approvedBusinessValue, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Business Done</small>
                            <h5 class="text-success">₹{{ number_format($doneBusinessValue, 2) }}</h5>
                            <small class="text-muted" style="font-size:10px; text-transform:none;">Dispatched Done + Fitting Done</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Total Received</small>
                            <h5 class="text-success">₹{{ number_format($receivedAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Payment Pending</small>
                            <h5 class="text-warning">₹{{ number_format($pendingAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Today Business Done</small>
                            <h5>₹{{ number_format($todayBusiness, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Two Column Section ── --}}
            <div class="row g-3 mb-3">

                {{-- Showroom Wise Business (Leads + Invoices) --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold">Showroom Wise Business</h6>
                            <small class="text-muted">Leads + Invoices combined</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Showroom</th>
                                            <th class="text-end">Lead Business (Done)</th>
                                            <th class="text-end">Invoice Amount</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showroomWiseBusiness as $row)
                                            <tr>
                                                <td>{{ optional($row->showroom)->strShowRoomName ?? 'N/A' }}</td>
                                                <td class="text-end">₹{{ number_format((float)($row->lead_amount ?? 0), 2) }}</td>
                                                <td class="text-end">₹{{ number_format((float)($row->invoice_amount ?? 0), 2) }}</td>
                                                <td class="text-end fw-bold">₹{{ number_format((float)($row->lead_amount ?? 0) + (float)($row->invoice_amount ?? 0), 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-3">No data found</td></tr>
                                        @endforelse
                                    </tbody>
                                    @if($showroomWiseBusiness->isNotEmpty())
                                    <tfoot class="table-success">
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format($showroomWiseBusiness->sum('lead_amount'), 2) }}</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format($showroomWiseBusiness->sum('invoice_amount'), 2) }}</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format($showroomWiseBusiness->sum(fn($r) => (float)($r->lead_amount ?? 0) + (float)($r->invoice_amount ?? 0)), 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales Executive Summary --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold">Sales Executive Summary</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Executive</th>
                                            <th class="text-end">Quotation</th>
                                            <th class="text-end">Done</th>
                                            <th class="text-end">Pending</th>
                                            <th class="text-end">Received</th>
                                            <th class="text-center">Leads</th>
                                            <th class="text-center">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesExecutiveSummary as $index => $row)
                                            <tr>
                                                <td>{{ $row['customer_name'] }}</td>
                                                <td>{{ $row['sales_executive_name'] }}</td>
                                                <td class="text-end">₹{{ number_format((float) $row['total_quotation_given'], 2) }}</td>
                                                <td class="text-end text-success fw-semibold">₹{{ number_format((float) $row['total_quotation_done'], 2) }}</td>
                                                <td class="text-end text-warning">₹{{ number_format((float) $row['total_payment_pending'], 2) }}</td>
                                                <td class="text-end text-success">₹{{ number_format((float) $row['total_payment_received'], 2) }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $row['lead_count'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#historyModal{{ $index }}"
                                                            title="View Leads">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center text-muted py-3">No data found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Invoice Report --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Invoice Report (Category / Product / Quantity / Amount)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Lead No</th>
                                    <th>Product Category</th>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoiceItems as $item)
                                    <tr>
                                        <td>{{ $item['lead_no'] }}</td>
                                        <td>{{ $item['product_category'] }}</td>
                                        <td>{{ $item['product'] }}</td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-end">₹{{ number_format((float)$item['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">No invoice data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Lead Detail Modals ────────────────────────────────────────────────────── --}}
@foreach($salesExecutiveSummary as $index => $row)
<div class="modal fade" id="historyModal{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-list-alt me-2 text-primary"></i>Lead Details — {{ $row['customer_name'] }}
                    </h5>
                    <small class="text-muted">Sales Executive: <strong>{{ $row['sales_executive_name'] }}</strong></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Summary strip --}}
                <div class="row g-0 border-bottom bg-light py-2 px-3">
                    <div class="col-3 text-center">
                        <small class="text-muted d-block">Total Leads</small>
                        <strong class="text-primary">{{ $row['lead_count'] }}</strong>
                    </div>
                    <div class="col-3 text-center">
                        <small class="text-muted d-block">Quotation Given</small>
                        <strong>₹{{ number_format((float)$row['total_quotation_given'], 2) }}</strong>
                    </div>
                    <div class="col-3 text-center">
                        <small class="text-muted d-block">Business Done</small>
                        <strong class="text-success">₹{{ number_format((float)$row['total_quotation_done'], 2) }}</strong>
                    </div>
                    <div class="col-3 text-center">
                        <small class="text-muted d-block">Received</small>
                        <strong class="text-success">₹{{ number_format((float)$row['total_payment_received'], 2) }}</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Lead No</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Next Follow Up</th>
                                <th class="text-end">Lead Amount</th>
                                <th class="text-end">Received</th>
                                <th class="text-end">Pending</th>
                                <th>Products</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row['history'] as $hIdx => $historyLead)
                                @php
                                    $received = (float) $historyLead->payments->sum('iPaidAmount');
                                    $pending  = max(0, (float) $historyLead->iLeadAmount - $received);
                                    $businessDoneStatuses = [
                                        \App\Support\LeadWorkflow::STATUS_DISPATCHED_DONE,
                                        \App\Support\LeadWorkflow::STATUS_FITTING_DONE,
                                        \App\Support\LeadWorkflow::STATUS_DEAL_DONE,
                                    ];
                                    $isDone = in_array($historyLead->iCurrentLeadStatus, $businessDoneStatuses);
                                @endphp
                                <tr class="{{ $isDone ? 'table-success' : '' }}">
                                    <td>{{ $hIdx + 1 }}</td>
                                    <td>
                                        <a href="{{ route('store.leads.histories.index', $historyLead->iLeadId) }}"
                                           target="_blank"
                                           class="fw-semibold text-primary text-decoration-none">
                                            {{ $historyLead->strLeadNo }}
                                            <i class="fas fa-external-link-alt ms-1" style="font-size:10px;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'Lead Rejected'       => 'bg-danger',
                                                'Deal Done'           => 'bg-success',
                                                'Fitting Done'        => 'bg-success',
                                                'Dispatched Done'     => 'bg-success',
                                                'Quotation Approved'  => 'bg-primary',
                                                'Advance Received'    => 'bg-info text-dark',
                                            ];
                                            $badgeCls = $statusColors[$historyLead->iCurrentLeadStatus] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeCls }}" style="font-size:10px;">
                                            {{ $historyLead->iCurrentLeadStatus }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap small">
                                        {{ \Carbon\Carbon::parse($historyLead->CreatedDate)->format('d-m-Y') }}
                                    </td>
                                    <td class="text-nowrap small">
                                        {{ $historyLead->NetFollowupdate
                                            ? \Carbon\Carbon::parse($historyLead->NetFollowupdate)->format('d-m-Y')
                                            : '—' }}
                                    </td>
                                    <td class="text-end">₹{{ number_format((float)$historyLead->iLeadAmount, 2) }}</td>
                                    <td class="text-end text-success">₹{{ number_format($received, 2) }}</td>
                                    <td class="text-end {{ $pending > 0 ? 'text-warning' : 'text-success' }}">₹{{ number_format($pending, 2) }}</td>
                                    <td>
                                        @php
                                            $activeBatch = optional($historyLead->quotation)->quotation_batch_id;
                                            $activeItems = $activeBatch
                                                ? $historyLead->quotations->where('quotation_batch_id', $activeBatch)->values()
                                                : $historyLead->quotations;
                                        @endphp
                                        @if($activeItems->count())
                                            <ul class="mb-0 ps-3" style="font-size:11px;">
                                                @foreach($activeItems as $qi)
                                                    <li>
                                                        {{ optional($qi->product)->strProductName ?? '—' }}
                                                        (Qty: {{ $qi->quantity ?? 1 }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Totals</th>
                                <th class="text-end">₹{{ number_format($row['history']->sum('iLeadAmount'), 2) }}</th>
                                <th class="text-end text-success">₹{{ number_format((float)$row['total_payment_received'], 2) }}</th>
                                <th class="text-end text-warning">₹{{ number_format((float)$row['total_payment_pending'], 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection