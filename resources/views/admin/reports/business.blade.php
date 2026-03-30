@extends('layouts.app')

@section('title', 'Business Report')

@section('styles')
<style>
    .metric-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    }
    .metric-card small {
        color: #64748b;
        font-weight: 600;
    }
    .metric-card h5 {
        margin-top: 6px;
        margin-bottom: 0;
        font-weight: 700;
    }
    .table thead th {
        white-space: nowrap;
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

            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Search</button>
                            <a href="{{ route('admin.reports.business') }}" class="btn btn-light border">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

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
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Showroom Wise Business</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Showroom</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showroomWiseBusiness as $row)
                                            <tr>
                                                <td>{{ optional($row->showroom)->strShowRoomName ?? 'N/A' }}</td>
                                                <td>₹{{ number_format((float)$row->total_amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="text-center text-muted">No data found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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
                                                <td>₹{{ number_format((float) $row['total_quotation_given'], 2) }}</td>
                                                <td>₹{{ number_format((float) $row['total_quotation_done'], 2) }}</td>
                                                <td>₹{{ number_format((float) $row['total_payment_pending'], 2) }}</td>
                                                <td>₹{{ number_format((float) $row['total_payment_received'], 2) }}</td>
                                                <td>{{ $row['lead_count'] }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $index }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>

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
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ $historyLead->strLeadNo }}</td>
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
                                        @empty
                                            <tr><td colspan="8" class="text-center text-muted">No data found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Invoice Report (Category / Product / Quantity / Amount)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Lead No</th>
                                    <th>Product Category</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoiceItems as $item)
                                    <tr>
                                        <td>{{ $item['lead_no'] }}</td>
                                        <td>{{ $item['product_category'] }}</td>
                                        <td>{{ $item['product'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>₹{{ number_format((float)$item['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No invoice data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection