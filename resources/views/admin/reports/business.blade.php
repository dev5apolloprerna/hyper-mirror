@extends('layouts.app')

@section('title', 'Business Report')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Admin Business Report</h4>
            </div>

            <div class="card mb-3">
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
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3"><div class="card"><div class="card-body"><small>Total Quotation Value</small><h5>₹{{ number_format($totalQuotationValue, 2) }}</h5></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><small>Approved Business</small><h5>₹{{ number_format($approvedBusinessValue, 2) }}</h5></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><small>Business Done (Approved)</small><h5>₹{{ number_format($doneBusinessValue, 2) }}</h5></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><small>Received Amount</small><h5>₹{{ number_format($receivedAmount, 2) }}</h5></div></div></div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-3"><div class="card"><div class="card-body"><small>Pending Amount</small><h5>₹{{ number_format($pendingAmount, 2) }}</h5></div></div></div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6"><div class="card"><div class="card-body"><small>Today Business (Deal Done)</small><h5>₹{{ number_format($todayBusiness, 2) }}</h5></div></div></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Showroom Wise Business</div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead><tr><th>Showroom</th><th>Amount</th></tr></thead>
                                <tbody>
                                    @forelse($showroomWiseBusiness as $row)
                                        <tr><td>{{ optional($row->showroom)->strShowRoomName ?? 'N/A' }}</td><td>₹{{ number_format((float)$row->total_amount, 2) }}</td></tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Sales Executive Summary</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Sales Executive</th>
                                    <th>Total Quotations</th>
                                    <th>Done</th>
                                    <th>Pending</th>
                                    <th>History</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesExecutiveSummary as $index => $row)
                                    <tr>
                                        <td>{{ $row['customer_names']->join(', ') ?: 'N/A' }}</td>
                                        <td>{{ $row['sales_executive_name'] }}</td>
                                        <td>{{ $row['total_quotations'] }}</td>
                                        <td>{{ $row['done_count'] }}</td>
                                        <td>{{ $row['pending_count'] }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $index }}">View</button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="historyModal{{ $index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header"><h5 class="modal-title">Quotation History</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <table class="table table-sm table-bordered">
                                                        <thead><tr><th>Lead</th><th>Status</th><th>Amount</th></tr></thead>
                                                        <tbody>
                                                            @foreach($row['history'] as $historyLead)
                                                                <tr>
                                                                    <td>{{ $historyLead->strLeadNo }}</td>
                                                                    <td>{{ $historyLead->iCurrentLeadStatus }}</td>
                                                                    <td>₹{{ number_format((float)$historyLead->iLeadAmount, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Invoice Report (Category / Product / Qty / Amount)</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead><tr><th>Lead No</th><th>Product Category</th><th>Product</th><th>Quantity</th><th>Amount</th></tr></thead>
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
@endsection
