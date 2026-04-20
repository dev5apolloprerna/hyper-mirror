@extends('layouts.app')

@section('title', 'Party Report')

@section('styles')
<style>
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
                    <h4 class="mb-sm-0">Admin Party Report</h4>
                    <p class="text-muted mb-0 mt-1">Party wise total amount, paid amount and unpaid amount.</p>
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
                        <div class="col-md-6 d-flex gap-2 align-items-end">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Search</button>
                            <a href="{{ route('admin.reports.party') }}" class="btn btn-light border">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Party Wise Summary</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Party Name</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Unpaid Amount</th>
                                    <th class="text-center">Leads</th>
                                    <th class="text-center">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partySummary as $index => $party)
                                    <tr>
                                        <td class="fw-semibold">{{ $party['party_name'] }}</td>
                                        <td class="text-end">₹{{ number_format((float) $party['total_amount'], 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format((float) $party['paid_amount'], 2) }}</td>
                                        <td class="text-end text-warning">₹{{ number_format((float) $party['unpaid_amount'], 2) }}</td>
                                        <td class="text-center">{{ $party['lead_count'] }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#partyLeadsModal{{ $index }}"
                                                    title="View all leads">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@foreach($partySummary as $index => $party)
<div class="modal fade" id="partyLeadsModal{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details - {{ $party['party_name'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lead No</th>
                                <th>Created Date</th>
                                <th>Executive</th>
                                <th>Showroom</th>
                                <th>Status</th>
                                <th class="text-end">Lead Amount</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Unpaid Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($party['leads'] as $lead)
                                @php
                                    $leadPaid = (float) $lead->payments->sum('iPaidAmount');
                                    $leadUnpaid = max(0, (float) ($lead->iLeadAmount ?? 0) - $leadPaid);
                                @endphp
                                <tr>
                                    <td>{{ $lead->strLeadNo ?? '-' }}</td>
                                    <td>
                                        @if($lead->CreatedDate)
                                            {{ \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ optional($lead->createdBy)->name ?? 'N/A' }}</td>
                                    <td>{{ optional($lead->showroom)->strShowRoomName ?? 'N/A' }}</td>
                                    <td>{{ $lead->iCurrentLeadStatus ?? '-' }}</td>
                                    <td class="text-end">₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</td>
                                    <td class="text-end">₹{{ number_format($leadPaid, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($leadUnpaid, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">No lead data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
