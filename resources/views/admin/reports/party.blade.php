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
                    <p class="text-muted mb-0 mt-1">Party wise quotation, approved process and payment details.</p>
                </div>
            </div>

            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">

                        <div class="col-md-3">
                             <label class="form-label">Party Name</label>
                            <input type="text" name="party_name" value="{{ $partyName ?? '' }}" class="form-control" placeholder="Enter party name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Mobile No</label>
                            <input type="text" name="mobile_no" value="{{ $mobileNo ?? '' }}" class="form-control" placeholder="Enter mobile no">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quotation</label>
                            <input type="text" name="quotation_search" value="{{ $quotationSearch ?? '' }}" class="form-control" placeholder="Lead no / amount">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Inquiry From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Inquiry To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                        </div>
                        <div class="col-md-1 d-flex gap-2 align-items-end">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            <a href="{{ route('admin.reports.party') }}" class="btn btn-light border">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0  shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Party Wise Summary</h6>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover  mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="p-2">Party Name</th>
                                    <th class="p-2">Mobile No</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Quotation Approved</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Unpaid Amount</th>
                                    <th class="text-center">Leads</th>
                                    <th class="text-center">Payment Entries</th>
                                    <th class="text-center">Last Payment Date</th>
                                    <th class="text-center">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partySummary as $index => $party)
                                    <tr>
                                        <td class="fw-semibold p-2">{{ $party['party_name'] }}</td>
                                        <td class="fw-semibold p-2">{{ $party['mobile'] }}</td>
                                        <td class="text-end">₹{{ number_format((float) $party['total_amount'], 2) }}</td>
                                        <td class="text-end text-primary">
                                            ₹{{ number_format((float) $party['approved_amount'], 2) }}
                                            <div class="small text-muted">({{ $party['approved_lead_count'] }} leads)</div>
                                        </td>
                                        <td class="text-end text-success">₹{{ number_format((float) $party['paid_amount'], 2) }}</td>
                                        <td class="text-end text-warning">₹{{ number_format((float) $party['unpaid_amount'], 2) }}</td>
                                        <td class="text-center">{{ $party['lead_count'] }}</td>
                                        <td class="text-center">{{ $party['payment_entry_count'] }}</td>
                                        <td class="text-center">
                                            @if($party['last_payment_date'])
                                                {{ \Carbon\Carbon::parse($party['last_payment_date'])->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                        <td colspan="9" class="text-center text-muted py-3">No data found</td>
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
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <div class="border rounded px-3 py-2 bg-light">
                            <small class="text-muted d-block">Party Total Paid Amount</small>
                            <strong class="text-success">₹{{ number_format((float) $party['paid_amount'], 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded px-3 py-2 bg-light">
                            <small class="text-muted d-block">Party Total Unpaid Amount</small>
                            <strong class="text-warning">₹{{ number_format((float) $party['unpaid_amount'], 2) }}</strong>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2 text-primary">Lead + Payment Data</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Row Type</th>
                                <th>Lead No</th>
                                <th>Date</th>
                                <th>Executive</th>
                                <th>Showrooms</th>
                                <th>Showroom</th>
                                <th>Status</th>
                                <th class="text-end">Lead Amount</th>
                                <th class="text-end">Payment Amount</th>
                                <th class="text-end text-success">Paid Amount</th>
                                <th class="text-end text-warning">Unpaid Amount</th>
                                <th>Payment Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($party['leads'] as $lead)
                                @php
                                    $leadPaid = (float) $lead->payments->sum('iPaidAmount');
                                    $leadUnpaid = max(0, (float) ($lead->iLeadAmount ?? 0) - $leadPaid);
                                    $leadPayments = $lead->payments
                                        ->sortByDesc('PaymentDate')
                                            ->values();
                                    $executiveName = trim((string) (optional($lead->createdBy)->full_name ?? '')) ?: (optional($lead->createdBy)->strUserName ?? optional($lead->createdBy)->name ?? 'N/A');
                                    $assignedShowrooms = optional($lead->createdBy)->showrooms
                                        ?->pluck('strShowRoomName')
                                        ->filter()
                                        ->implode(', ') ?: 'N/A';
                                    $showroomName = optional($lead->showroom)->strShowRoomName ?? 'N/A';
                                @endphp
                                <tr>
                                    <td><span class="badge bg-primary">Lead</span></td>
                                    <td>{{ $lead->strLeadNo ?? '-' }}</td>
                                    <td>
                                        @if($lead->CreatedDate)
                                            {{ \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $executiveName }}</td>
                                    <td>{{ $assignedShowrooms }}</td>
                                    <td>{{ $showroomName }}</td>
                                    <td>{{ $lead->iCurrentLeadStatus ?? '-' }}</td>
                                    <td class="text-end">₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">₹{{ number_format($leadPaid, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($leadUnpaid, 2) }}</td>
                                    <td>-</td>
                                </tr>

                                @foreach($leadPayments as $payment)
                                    <tr class="table-light">
                                        <td><span class="badge bg-success">Payment</span></td>
                                        <td>{{ $lead->strLeadNo ?? '-' }}</td>
                                        <td>
                                            @if($payment->PaymentDate)
                                                {{ \Carbon\Carbon::parse($payment->PaymentDate)->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $executiveName }}</td>
                                        <td>{{ $showroomName }}</td>
                                        <td>{{ $lead->iCurrentLeadStatus ?? '-' }}</td>
                                        <td class="text-end">₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format((float)($payment->iPaidAmount ?? 0), 2) }}</td>
                                        <td class="text-end">₹{{ number_format($leadPaid, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($leadUnpaid, 2) }}</td>
                                        <td>{{ $payment->PaymentMode ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-3">No lead data found</td>
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