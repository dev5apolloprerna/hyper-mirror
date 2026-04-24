@extends('layouts.app')
@section('title', 'Lead Report')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">Admin Lead Report</h4>
                    <p class="text-muted mb-0 small">Read-only lead report with lead history, quotation history, and payment details.</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('admin.reports.leads') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1 small fw-semibold">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Lead / customer / mobile / showroom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1 small fw-semibold">Sales Person</label>
                            <select name="sales_person" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach($salesPersons as $salesPerson)
                                    @php
                                        $salesPersonName = $salesPerson->strUserName
                                            ?: trim(($salesPerson->first_name ?? '') . ' ' . ($salesPerson->last_name ?? ''))
                                            ?: ($salesPerson->name ?? 'N/A');
                                    @endphp
                                    <option value="{{ $salesPerson->id }}" {{ (string) request('sales_person') === (string) $salesPerson->id ? 'selected' : '' }}>
                                        {{ $salesPersonName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1 small fw-semibold">Sales Person Search</label>
                            <input type="text" name="sales_person_search" value="{{ request('sales_person_search') }}" class="form-control form-control-sm" placeholder="Sales person name">
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
                            <button type="submit" class="btn btn-success btn-sm flex-fill"><i class="fas fa-search me-1"></i>Search</button>
                            <a href="{{ route('admin.reports.leads') }}" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Sales Person Wise Lead Data</strong></div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sales Person</th>
                                <th>Showroom</th>
                                <th>Total Quotation Amount</th>
                                <th>Total Payment Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesPersonWiseData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['sales_person_name'] }}</td>
                                    <td>{{ $row['showroom_name'] }}</td>
                                    <td>₹{{ number_format((float) $row['quotation_total'], 2) }}</td>
                                    <td>₹{{ number_format((float) $row['payment_total'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">No sales person data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Lead No</th>
                                <th>Customer</th>
                                <th>Sales Person</th>
                                <th>Assigned Showrooms</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Lead Amount</th>
                                <th>Quotation Entries</th>
                                <th>Payment Received</th>
                                <th>History Entries</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                                <tr>
                                    <td>{{ $leads->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $lead->strLeadNo }}</td>
                                    <td>
                                        {{ optional($lead->customer)->strCustomer ?? '—' }}<br>
                                        <small class="text-muted">{{ optional($lead->customer)->strMobile ?? '—' }}</small>
                                    </td>
                                    <td>{{ optional($lead->createdBy)->strUserName ?: optional($lead->createdBy)->name ?: '—' }}</td>
                                    <td>
                                        {{
                                            optional($lead->createdBy)->showrooms
                                                ? optional($lead->createdBy)->showrooms->pluck('strShowRoomName')->filter()->unique()->implode(', ')
                                                : '—'
                                        }}
                                    </td>
                                    <td><span class="badge bg-info text-dark">{{ $lead->iCurrentLeadStatus }}</span></td>
                                    <td>
                                        @if(!empty($lead->last_history_entry))
                                            {{ \Carbon\Carbon::parse($lead->last_history_entry)->format('d-m-Y') }}
                                        @elseif(!empty($lead->updated_at))
                                            {{ $lead->updated_at->format('d-m-Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>                                    
                                    <td>₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</td>
                                    <td>{{ $lead->quotations->count() }}</td>
                                    <td>₹{{ number_format((float)$lead->payments->sum('iPaidAmount'), 2) }}</td>
                                    <td>{{ $lead->histories->count() }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('admin.reports.leads.show', $lead->iLeadId) }}" class="btn btn-sm btn-outline-primary" title="Lead detail"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.reports.leads.histories', $lead->iLeadId) }}" class="btn btn-sm btn-outline-info" title="Lead history"><i class="fas fa-history"></i></a>
                                            <a href="{{ route('admin.reports.leads.quotations', $lead->iLeadId) }}" class="btn btn-sm btn-outline-secondary" title="Quotation history"><i class="fas fa-file-invoice"></i></a>
                                            <a href="{{ route('admin.reports.leads.payments', $lead->iLeadId) }}" class="btn btn-sm btn-outline-dark" title="Payment history"><i class="fas fa-money-bill-wave"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center py-4 text-muted">No leads found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $leads->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
