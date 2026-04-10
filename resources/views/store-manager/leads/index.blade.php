@extends('layouts.app')

<!-- @section('title', $roleSlug === 'storemanager' ? 'Lead Management' : ucfirst($roleSlug ?? '') . ' Queue') -->
@section('title', $roleSlug === 'storemanager' ? 'Sales Management' : ucfirst($roleSlug ?? '') . ' Queue')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            @php
                                $queueLabels = [
                                    'storemanager' => 'Sales Management',                                    'measurement'  => 'Measurement Queue',
                                    'production'   => 'Production Queue',
                                    'dispatch'     => 'Dispatch Queue',
                                    'fitting'      => 'Fitting Queue',
                                    'account'      => 'Accounts Queue',
                                ];
                                $queueDescriptions = [
                                    'storemanager' => 'Create and manage all sales leads through the full workflow.',
                                    'measurement'  => 'Leads awaiting measurement visit. Update status once done.',
                                    'production'   => 'Leads pending production acceptance and dispatch readiness.',
                                    'dispatch'     => 'Leads ready to be dispatched to customers.',
                                    'fitting'      => 'Leads awaiting fitting completion.',
                                    'account'      => 'Leads for payment tracking and account management.',
                                ];
                            @endphp
                            <h4 class="mb-sm-0">{{ $queueLabels[$roleSlug] ?? 'Lead List' }}</h4>
                            <p class="mb-0 mt-1 text-muted small">{{ $queueDescriptions[$roleSlug] ?? '' }}</p>
                        </div>
                        <div class="page-title-right">
                            @if($roleSlug === 'storemanager')
                                <a href="{{ route('store.leads.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Add New Lead
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Filters ── --}}
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('store.leads.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label mb-1 small fw-semibold">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   value="{{ request('search') }}"
                                   placeholder="Lead no / customer / mobile / status">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>
                                        {{ $statusOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 small fw-semibold">Follow-up Bucket</label>
                            <select name="followup" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="today" {{ request('followup') === 'today' ? 'selected' : '' }}>
                                    Today's Follow-up
                                </option>
                                <option value="overdue" {{ request('followup') === 'overdue' ? 'selected' : '' }}>
                                    Overdue Follow-up
                                </option>
                            </select>
                        </div>
                         <div class="col-md-2">
                            <label class="form-label mb-1 small fw-semibold">From Date</label>
                            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1 small fw-semibold">To Date</label>
                            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                        </div>

                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-success btn-sm flex-fill">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Lead Table ── --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Lead No</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    @if(in_array($roleSlug, ['storemanager', 'account']))
                                        <th>Lead Amount</th>
                                    @endif
                                    <th>Next Follow Up</th>
                                    @if(in_array($roleSlug, ['storemanager', 'measurement']))
                                        <th>Measurement</th>
                                    @endif
                                    <th style="min-width:160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    @php
                                        $today = now()->toDateString();
                                        $isOverdue = $lead->NetFollowupdate && $lead->NetFollowupdate < $today;
                                        $isToday   = $lead->NetFollowupdate && $lead->NetFollowupdate === $today;

                                        $statusBadgeMap = [
                                            'In Measurement'      => 'bg-warning text-dark',
                                            'Measurement Done'    => 'bg-info text-dark',
                                            'In Design'           => 'bg-primary',
                                            'Quotation Sent'      => 'bg-secondary',
                                            'Lead Rejected'       => 'bg-danger',
                                            'Quotation Approved'  => 'bg-success',
                                            'Advance Received'    => 'bg-success',
                                            'Production Accepted' => 'bg-warning text-dark',
                                            'Ready to Dispatched' => 'bg-info text-dark',
                                            'Received @ Narol'    => 'bg-dark',
                                            'Dispatched'          => 'bg-primary',
                                            'Dispatched Done'     => 'bg-success',
                                            'Fitting Pending'     => 'bg-warning text-dark',
                                            'Fitting Done'        => 'bg-success',
                                        ];
                                        $badgeClass = $statusBadgeMap[$lead->iCurrentLeadStatus] ?? 'bg-secondary';
                                    @endphp
                                    <tr class="{{ $isOverdue ? 'table-danger' : ($isToday ? 'table-warning' : '') }}">
                                        <td class="ps-3 fw-semibold">{{ $lead->strLeadNo }}</td>
                                        <td>{{ $lead->customer->strCustomer ?? '—' }}</td>
                                        <td>{{ $lead->customer->strMobile ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $lead->iCurrentLeadStatus }}
                                            </span>
                                        </td>
                                        @if(in_array($roleSlug, ['storemanager', 'account']))
                                            <td>₹{{ number_format((float)$lead->iLeadAmount, 2) }}</td>
                                        @endif
                                        <td>
                                            @if($lead->NetFollowupdate)
                                                <span class="{{ $isOverdue ? 'text-danger fw-bold' : ($isToday ? 'text-warning fw-bold' : '') }}">
                                                    {{ date('d-m-Y',strtotime($lead->NetFollowupdate)) }}
                                                </span>
                                                @if($isOverdue)
                                                    <span class="badge bg-danger ms-1 small">Overdue</span>
                                                @elseif($isToday)
                                                    <span class="badge bg-warning text-dark ms-1 small">Today</span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        @if(in_array($roleSlug, ['storemanager', 'measurement']))
                                            <td>
                                                <span class="badge {{ $lead->IsMeasureMentRequired ? 'bg-info text-dark' : 'bg-light text-muted border' }}">
                                                    {{ $lead->IsMeasureMentRequired ? 'Required' : 'Not Required' }}
                                                </span>
                                            </td>
                                        @endif
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                {{-- Primary action: open lead workflow --}}
                                                <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="Open Lead Workflow">
                                                    <i class="fas fa-tasks"></i>
                                                    @if(in_array($roleSlug, ['measurement','production','dispatch','fitting']))
                                                        <span class="ms-1 d-none d-md-inline">Open</span>
                                                    @endif
                                                </a>
                                                @if($roleSlug === 'storemanager')
                                                    <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="View Full Lead Detail">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="ms-1 d-none d-md-inline"></span>
                                                    </a>
                                                @endif

                                                {{-- Quotation view --}}
                                                @if($lead->quotation)
                                                    <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="View Quotation">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                    <a href="{{ route('store.leads.quotation-pdf', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-danger"
                                                       title="Quotation PDF"
                                                       target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif

                                                {{-- Store manager extras --}}
                                                @if($roleSlug === 'storemanager')
                                                    <a href="{{ route('store.leads.quotation', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Manage Quotation">
                                                        <i class="fas fa-file-signature"></i>
                                                    </a>
                                                    <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-warning"
                                                       title="Lead Designs">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                @endif

                                                {{-- Payment access --}}
                                                @if(in_array($roleSlug, ['storemanager', 'account']))
                                                    <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}"
                                                       class="btn btn-sm btn-outline-dark"
                                                       title="Lead Payments">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-3 d-block opacity-25"></i>
                                            No leads found in your queue.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
