@extends('layouts.app')
@section('title', 'Lead Workflow — ' . $lead->strLeadNo)

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            {{-- Page Header --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $lead->strLeadNo }}</h4>
                            <p class="mb-0 mt-1 text-muted">
                                <strong>{{ $lead->customer->strCustomer ?? '—' }}</strong>
                                &nbsp;·&nbsp;
                                {{ $lead->customer->strMobile ?? '' }}
                                &nbsp;·&nbsp;
                                <span class="badge bg-primary text-white">{{ $lead->iCurrentLeadStatus }}</span>
                                @if($lead->NetFollowupdate && !in_array($lead->iCurrentLeadStatus, ['Lead Rejected', 'Deal Done', 'Measurement Done']))
                                    &nbsp;·&nbsp;
                                    <span class="text-{{ now()->toDateString() > $lead->NetFollowupdate ? 'danger' : 'success' }}">
                                        <i class="fas fa-calendar-alt me-1"></i>Next: {{ \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="page-title-right d-flex gap-2 flex-wrap">
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

                            @if($roleSlug === 'storemanager')
                                <a href="{{ route('store.leads.quotation', $lead->iLeadId) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-signature"></i> Quotation
                                </a>

                                @if($lead->quotation)
                                    <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-file-invoice"></i> View Quotation
                                    </a>
                                    
                                @endif

                                <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-images"></i> Designs
                                </a>
                            @endif

                            @if(in_array($roleSlug, ['storemanager', 'account']))
                                @if($lead->quotation)
                                    <a href="{{ route('store.leads.quotation-pdf', $lead->iLeadId) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                @endif
                                <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="btn btn-dark btn-sm">
                                    <i class="fas fa-money-bill-wave"></i> Payments
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOP SECTION --}}
            <div class="row g-3">

                {{-- LEFT: Lead Summary --}}
                <div class="col-xl-7 col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Lead Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted" style="width:45%">Customer</td>
                                            <td><strong>{{ $lead->customer->strCustomer ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Mobile</td>
                                            <td>{{ $lead->customer->strMobile ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Status</td>
                                            <td>
                                                <span class="badge bg-info text-dark">{{ $lead->iCurrentLeadStatus }}</span>
                                                @if($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_LEAD_REJECTED)
                                                    <span class="badge bg-danger ms-1">Rejected</span>
                                                @elseif($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_DEAL_DONE)
                                                    <span class="badge bg-success ms-1">Closed</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Measurement</td>
                                            <td>{{ $lead->IsMeasureMentRequired ? 'Required' : 'Not Required' }}</td>
                                        </tr>
                                        @if($lead->MeasurementVisitDate)
                                        <tr>
                                            <td class="text-muted">Measurement Date</td>
                                            <td>{{ \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        @if($lead->NetFollowupdate && !in_array($lead->iCurrentLeadStatus, [\App\Support\LeadWorkflow::STATUS_LEAD_REJECTED, \App\Support\LeadWorkflow::STATUS_DEAL_DONE, \App\Support\LeadWorkflow::STATUS_MEASUREMENT_DONE]))
                                        <tr>
                                            <td class="text-muted" style="width:45%">Next Follow Up</td>
                                            <td class="{{ now()->toDateString() > $lead->NetFollowupdate ? 'text-danger fw-bold' : '' }}">
                                                {{ \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') }}
                                                @if(now()->toDateString() > $lead->NetFollowupdate)
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @elseif(now()->toDateString() === $lead->NetFollowupdate)
                                                    <span class="badge bg-warning text-dark ms-1">Today</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-muted">Lead Amount</td>
                                            <td><strong>₹{{ number_format((float)$lead->iLeadAmount, 2) }}</strong></td>
                                        </tr>
                                        @if($lead->iFittingCharges)
                                        <tr>
                                            <td class="text-muted">Fitting Charges</td>
                                            <td>₹{{ number_format((float)$lead->iFittingCharges, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if(in_array($roleSlug, ['storemanager', 'account']))
                                        <tr>
                                            <td class="text-muted">Total Paid</td>
                                            <td>
                                                @php $totalPaid = $lead->payments()->sum('iPaidAmount'); @endphp
                                                <strong class="{{ $totalPaid >= $lead->iLeadAmount && $lead->iLeadAmount > 0 ? 'text-success' : 'text-warning' }}">
                                                    ₹{{ number_format((float)$totalPaid, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($lead->SiteAddress)
                                        <tr>
                                            <td class="text-muted">Site Address</td>
                                            <td>{{ $lead->SiteAddress }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Status Update Form --}}
                <div class="col-xl-5 col-lg-5">
                    @if($isReadOnly)
                        <div class="alert alert-{{ in_array($lead->iCurrentLeadStatus, [\App\Support\LeadWorkflow::STATUS_LEAD_REJECTED]) ? 'danger' : (in_array($lead->iCurrentLeadStatus, [\App\Support\LeadWorkflow::STATUS_DEAL_DONE]) ? 'success' : 'secondary') }} mb-0">
                            <i class="fas fa-lock me-2"></i>
                            @if($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_LEAD_REJECTED)
                                This lead has been <strong>rejected</strong>. No further changes are possible.
                            @elseif($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_DEAL_DONE)
                                This lead is <strong>closed (Deal Done)</strong>. No further changes are possible.
                            @elseif(\App\Support\LeadWorkflow::readOnlyForRole($roleSlug, $lead->iCurrentLeadStatus))
                                Your role can view this lead but cannot change its status at this stage.
                            @else
                                No status transitions available for your role at this stage.
                            @endif
                        </div>

                        @if($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_DEAL_DONE && in_array($roleSlug, ['storemanager', 'account']))
                            @php $totalPaid = $lead->payments()->sum('iPaidAmount'); @endphp
                            <div class="card border-0 shadow-sm mt-3">
                                <div class="card-body">
                                    <h6 class="fw-bold text-success"><i class="fas fa-check-circle me-2"></i>Deal Summary</h6>
                                    <p class="mb-1">Quotation Amount: <strong>₹{{ number_format((float)$lead->iLeadAmount, 2) }}</strong></p>
                                    <p class="mb-0">Total Received: <strong>₹{{ number_format((float)$totalPaid, 2) }}</strong></p>
                                </div>
                            </div>
                        @endif

                    @elseif(!empty($allowedStatuses))
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-exchange-alt me-2 text-success"></i>Update Status / Add Remark
                                </h6>
                            </div>
                            <div class="card-body">

                                @if(!$canCloseDeal && in_array(\App\Support\LeadWorkflow::STATUS_DEAL_DONE, \App\Support\LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead)))
                                    @php $totalPaid = $lead->payments()->sum('iPaidAmount'); @endphp
                                    <div class="alert alert-warning py-2 small mb-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <strong>Deal Done</strong> is not available yet.<br>
                                        Paid: ₹{{ number_format((float)$totalPaid, 2) }} /
                                        Required: ₹{{ number_format((float)$lead->iLeadAmount, 2) }}
                                    </div>
                                @endif

                                <form method="POST"
                                      action="{{ route('store.leads.histories.store', $lead->iLeadId) }}"
                                      id="historyForm">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select name="iStatus" id="iStatus"
                                                class="form-select @error('iStatus') is-invalid @enderror"
                                                required>
                                            <option value="">— Select status —</option>
                                            @foreach($allowedStatuses as $status)
                                                <option value="{{ $status }}" {{ old('iStatus') == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('iStatus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Rejection Reason (shown only for Lead Rejected) --}}
                                    <div class="mb-3" id="rejectionReasonWrapper" style="display:none;">
                                        <label class="form-label fw-semibold text-danger">
                                            Rejection Reason <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="rejection_reason" id="rejection_reason"
                                                  class="form-control @error('rejection_reason') is-invalid @enderror"
                                                  rows="3"
                                                  placeholder="Enter the reason for rejecting this lead...">{{ old('rejection_reason') }}</textarea>
                                        @error('rejection_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Follow-up Date (hidden for Rejected, Measurement Done, Deal Done) --}}
                                    <div class="mb-3" id="followupDateWrapper" style="display:none;">
                                        <label class="form-label fw-semibold" id="followupDateLabel">
                                            Next Follow Up Date
                                            <span class="text-danger" id="followupRequired" style="display:none;">*</span>
                                        </label>
                                        <input type="date"
                                               name="NetFolloupwdate"
                                               id="NetFolloupwdate"
                                               class="form-control @error('NetFolloupwdate') is-invalid @enderror"
                                               value="{{ old('NetFolloupwdate') }}">
                                        @error('NetFolloupwdate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted" id="followupHint"></small>
                                    </div>
 <div class="mb-3" id="expectedDeliveryWrapper" style="display:none;">
                                        <label class="form-label fw-semibold">
                                            Expected Delivery Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="expected_delivery_date"
                                               id="expected_delivery_date"
                                               class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                               value="{{ old('expected_delivery_date', $lead->expected_delivery_date) }}">
                                        @error('expected_delivery_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Comments / Remarks <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="strComments"
                                                  class="form-control @error('strComments') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="Enter your remarks..."
                                                  required>{{ old('strComments') }}</textarea>
                                        @error('strComments')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-1"></i> Save Update
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            No status transitions available for your role at this stage.
                        </div>
                    @endif
                </div>
            </div>

            {{-- BOTTOM: LEAD HISTORY (read-only — no delete) --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-history me-2 text-secondary"></i>Lead History
                            </h6>
                        </div>

                        <div class="card-body p-0">
                            @if($histories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Status</th>
                                                <th>Next Follow Up</th>
                                                <th>Entered By</th>
                                                <th>Comments</th>
                                                <th>Entry Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($histories as $history)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'In Measurement'      => 'background:#fff3cd;color:#856404;',
                                                                'Measurement Done'    => 'background:#cff4fc;color:#055160;',
                                                                'In Design'           => 'background:#cfe2ff;color:#084298;',
                                                                'Quotation Sent'      => 'background:#e2e3e5;color:#41464b;',
                                                                'Lead Rejected'       => 'background:#f8d7da;color:#842029;',
                                                                'Quotation Approved'  => 'background:#d1e7dd;color:#0f5132;',
                                                                'Advance Received'    => 'background:#d1e7dd;color:#0a3622;',
                                                                'Production Accepted' => 'background:#fff3cd;color:#664d03;',
                                                                'Ready to Dispatched' => 'background:#cff4fc;color:#055160;',
                                                                'Dispatched'          => 'background:#cfe2ff;color:#084298;',
                                                                'Dispatched Done'     => 'background:#d1e7dd;color:#0f5132;',
                                                                'Fitting Pending'     => 'background:#fff3cd;color:#856404;',
                                                                'Fitting Done'        => 'background:#d1e7dd;color:#0a3622;',
                                                                'Deal Done'           => 'background:#198754;color:#fff;',
                                                            ];
                                                            $styleStr = $statusColors[$history->iStatus] ?? 'background:#e2e3e5;color:#41464b;';
                                                        @endphp
                                                        <span class="badge px-2 py-1" style="{{ $styleStr }}">
                                                            {{ $history->iStatus }}
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        {{ $history->NetFolloupwdate
                                                            ? \Carbon\Carbon::parse($history->NetFolloupwdate)->format('d-m-Y')
                                                            : '—' }}
                                                    </td>
                                                    <td>
                                                        {{ $history->user->full_name ?? ($history->user->strUserName ?? '—') }}
                                                    </td>
                                                    <td style="min-width:260px; white-space:pre-wrap;">{{ $history->strComments ?: '—' }}</td>
                                                    <td class="text-nowrap">
                                                        {{ $history->EntryDate
                                                            ? \Carbon\Carbon::parse($history->EntryDate)->format('d-m-Y')
                                                            : '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-3 py-3">{{ $histories->links() }}</div>
                            @else
                                <div class="p-5 text-center text-muted">
                                    <i class="fas fa-history fa-2x mb-3 opacity-25 d-block"></i>
                                    No history recorded yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const followupRequiredStatuses = @json(\App\Support\LeadWorkflow::followupRequiredStatuses());
const NO_FOLLOWUP_STATUSES = [
    'Lead Rejected',
    'Measurement Done',
    'Deal Done',
    'Dispatched Done',
    'Fitting Done',
];

const statusHints = {
    'In Measurement'      : 'Enter the measurement visit date.',
    'In Design'           : 'Enter the design follow-up date.',
    'Quotation Sent'      : 'Enter the quotation follow-up date.',
    'Quotation Approved'  : 'Enter the advance payment follow-up date.',
    'Advance Received'    : 'Enter the expected production / follow-up date.',
    'Production Accepted' : 'Enter the expected dispatch date.',
    'Ready to Dispatched' : 'Enter the dispatch date.',
    'Dispatched'          : 'Enter the fitting schedule date.',
    'Fitting Pending'     : 'Enter the fitting date.',
};

const statusSelect        = document.getElementById('iStatus');
const followupWrapper     = document.getElementById('followupDateWrapper');
const followupInput       = document.getElementById('NetFolloupwdate');
const followupReqStar     = document.getElementById('followupRequired');
const followupHint        = document.getElementById('followupHint');
const rejectionWrapper    = document.getElementById('rejectionReasonWrapper');
const rejectionInput      = document.getElementById('rejection_reason');
const expectedDeliveryWrapper = document.getElementById('expectedDeliveryWrapper');
const expectedDeliveryInput = document.getElementById('expected_delivery_date');

function updateFormFields() {
    if (!statusSelect) return;

    const selected   = statusSelect.value;
    const isReject   = selected === 'Lead Rejected';
    const isDealDone = selected === 'Deal Done';
    const isQuotationApproved = selected === 'Quotation Approved';
    const noFollowup = NO_FOLLOWUP_STATUSES.includes(selected) || !selected;
    const isRequired = followupRequiredStatuses.includes(selected);

    // Rejection reason
    if (rejectionWrapper) {
        rejectionWrapper.style.display = isReject ? 'block' : 'none';
        if (rejectionInput) rejectionInput.required = isReject;
    }

    // Follow-up date
    if (followupWrapper) {
        followupWrapper.style.display = noFollowup ? 'none' : 'block';
    }
    if (followupReqStar) {
        followupReqStar.style.display = isRequired && !noFollowup ? 'inline' : 'none';
    }
    if (followupInput) {
        followupInput.required = isRequired && !noFollowup;
        if (noFollowup) followupInput.value = '';
    }
    if (followupHint) {
        followupHint.textContent = noFollowup ? '' : (statusHints[selected] || '');
    }
     if (expectedDeliveryWrapper) {
        expectedDeliveryWrapper.style.display = isQuotationApproved ? 'block' : 'none';
    }
    if (expectedDeliveryInput) {
        expectedDeliveryInput.required = isQuotationApproved;
        if (!isQuotationApproved) expectedDeliveryInput.value = '';
    }
}

if (statusSelect) {
    statusSelect.addEventListener('change', updateFormFields);
    updateFormFields();
}
</script>
@endsection
