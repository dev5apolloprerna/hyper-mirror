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
        font-size: 16px;
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
                    <h4 class="mb-sm-0">Admin Business Report</h4>
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
                            <small>Total Quotation</small>
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
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Received</small>
                            <h5 class="text-success">₹{{ number_format($receivedAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Pending</small>
                            <h5 class="text-warning">₹{{ number_format($pendingAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xl-2">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Today Deal Done</small>
                            <h5>₹{{ number_format($todayBusiness, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

                        <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Daily Business Cash</small>
                            <h5 class="text-success">₹{{ number_format($todayCashAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Daily Business Bank</small>
                            <h5 class="text-primary">₹{{ number_format($todayBankAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card metric-card h-100">
                        <div class="card-body">
                            <small>Daily Business Total</small>
                            <h5>₹{{ number_format($todayTotalAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Two Column Section ── --}}
            <div class="row g-3 mb-3">

                {{-- Showroom Wise Business --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Showroom Wise Business</h6>
                            <small class="text-muted">Invoice item-wise details by branch</small>                   </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Showroom</th>
                                            <th>Sales Manager</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showroomWiseBusiness as $row)
                                            <tr>
                                                <td>{{ $row['branch_name'] }}</td>
                                                <td>{{ $row['sales_manager_name'] }}</td>
                                                <td>{{ $row['category'] }}</td>
                                                <td>{{ $row['product'] }}</td>
                                                <td class="text-center">{{ rtrim(rtrim(number_format((float)$row['quantity'], 2, '.', ''), '0'), '.') }}</td>
                                                <td class="text-end">₹{{ number_format((float)($row['amount'] ?? 0), 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-3">No data found</td></tr>                                       
                                     @endforelse
                                     </tbody>
                                    @if($showroomWiseBusiness->isNotEmpty())
                                    <tfoot class="table-success">
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td colspan="3"></td>
                                            <td class="text-center"><strong>{{ rtrim(rtrim(number_format((float)$showroomWiseBusiness->sum('quantity'), 2, '.', ''), '0'), '.') }}</strong></td>
                                            <td class="text-end"><strong>₹{{ number_format((float)$showroomWiseBusiness->sum('amount'), 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sales Executive Summary --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Sales Executive Summary</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Executive</th>
                                            <th>Showroom</th>
                                            <th>Quotation Given</th>
                                            <th>Done</th>
                                            <th>Pending</th>
                                            <th>Received</th>
                                            <th>Leads</th>
                                            <th>History</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesExecutiveSummary as $index => $row)
                                            <tr>
                                                <td>{{ $row['customer_name'] }}</td>
                                                <td>{{ $row['sales_executive_name'] }}</td>
                                                <td>{{ $row['showroom_names'] }}</td>
                                                <td>₹{{ number_format((float) $row['total_quotation_given'], 2) }}</td>
                                                <td class="text-success fw-semibold">₹{{ number_format((float) $row['total_quotation_done'], 2) }}</td>
                                                <td class="text-warning">₹{{ number_format((float) $row['total_payment_pending'], 2) }}</td>
                                                <td class="text-success">₹{{ number_format((float) $row['total_payment_received'], 2) }}</td>
                                                <td>{{ $row['lead_count'] }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#historyModal{{ $index }}"
                                                            title="View History">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9" class="text-center text-muted py-3">No data found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Lead History Modals ── --}}
@foreach($salesExecutiveSummary as $index => $row)
<div class="modal fade" id="historyModal{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead History - {{ $row['customer_name'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lead</th>
                                <th>Showroom</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Received</th>
                                <th>Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row['history'] as $historyLead)
                                @php
                                    $received = (float) $historyLead->payments->sum('iPaidAmount');
                                    $pending = max(0, (float) $historyLead->iLeadAmount - $received);
                                    $leadShowroomName = $showroomNameMap->get($historyLead->iShowroomId) ?? 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $historyLead->strLeadNo }}</td>
                                    <td>{{ $leadShowroomName }}</td>
                                    <td>{{ $historyLead->iCurrentLeadStatus }}</td>
                                    <td>₹{{ number_format((float)$historyLead->iLeadAmount, 2) }}</td>
                                    <td>₹{{ number_format($received, 2) }}</td>
                                    <td>₹{{ number_format($pending, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection