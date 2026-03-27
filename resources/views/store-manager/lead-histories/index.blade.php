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
                                @if($lead->NetFollowupdate)
                                    &nbsp;·&nbsp;
                                    <span class="text-{{ now()->toDateString() > $lead->NetFollowupdate ? 'danger' : 'success' }}">
                                        <i class="fas fa-calendar-alt me-1"></i>Next: {{ $lead->NetFollowupdate }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="page-title-right d-flex gap-2 flex-wrap">
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Leads
                            </a>

                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                <a href="{{ route('store.leads.quotation', $lead->iLeadId) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-signature"></i> Quotation
                                </a>

                                <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-images"></i> Designs
                                </a>
                            @endif

                            @if(
                                optional(auth()->user()->crmRole)->slug === 'storemanager' ||
                                optional(auth()->user()->crmRole)->slug === 'account'
                            )
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
                                <div class="col-md-6 mb-3">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted" style="width:45%">Customer Name</td>
                                            <td><strong>{{ $lead->customer->strCustomer ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Mobile</td>
                                            <td>{{ $lead->customer->strMobile ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Current Status</td>
                                            <td>
                                                <span class="badge bg-info text-dark">{{ $lead->iCurrentLeadStatus }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Lead Amount</td>
                                            <td><strong>₹{{ number_format((float)$lead->iLeadAmount, 2) }}</strong></td>
                                        </tr>
                                        @if($lead->iFittingCharges)
                                        <tr>
                                            <td class="text-muted">Fitting Charges</td>
                                            <td><strong>₹{{ number_format((float)$lead->iFittingCharges, 2) }}</strong></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-muted">Measurement Required</td>
                                            <td>{{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <table class="table table-sm table-borderless mb-0">
                                        @if($lead->MeasurementVisitDate)
                                        <tr>
                                            <td class="text-muted" style="width:45%">Measurement Date</td>
                                            <td>{{ $lead->MeasurementVisitDate }}</td>
                                        </tr>
                                        @endif

                                        @if($lead->NetFollowupdate)
                                        <tr>
                                            <td class="text-muted">Next Follow Up</td>
                                            <td class="{{ now()->toDateString() > $lead->NetFollowupdate ? 'text-danger fw-bold' : '' }}">
                                                {{ $lead->NetFollowupdate }}
                                                @if(now()->toDateString() > $lead->NetFollowupdate)
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @elseif(now()->toDateString() === $lead->NetFollowupdate)
                                                    <span class="badge bg-warning text-dark ms-1">Today</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if($lead->SiteAddress)
                                        <tr>
                                            <td class="text-muted">Site Address</td>
                                            <td>{{ $lead->SiteAddress }}</td>
                                        </tr>
                                        @endif

                                        @if($lead->quotation)
                                        <tr>
                                            <td class="text-muted">Quotation</td>
                                            <td>
                                                <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}"
                                                   class="btn btn-outline-secondary btn-sm py-0 px-2">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td class="text-muted">Lead No</td>
                                            <td>{{ $lead->strLeadNo }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Update Status / Add Remark --}}
                <div class="col-xl-5 col-lg-5">
                    @if(!empty($allowedStatuses))
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-exchange-alt me-2 text-success"></i>Update Status / Add Remark
                            </h6>
                        </div>

                        <div class="card-body">
                            <form method="POST"
                                  action="{{ route('store.leads.histories.store', $lead->iLeadId) }}"
                                  id="historyForm">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <select name="iStatus"
                                            id="iStatus"
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

                                <div class="mb-3" id="followupDateWrapper">
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

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Comments / Remarks <span class="text-danger">*</span></label>
                                    <textarea name="strComments"
                                              class="form-control @error('strComments') is-invalid @enderror"
                                              rows="5"
                                              placeholder="Enter your remarks here..."
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
                    <div class="alert alert-secondary">
                        <i class="fas fa-lock me-2"></i>
                        No status transitions available for your role at this stage.
                    </div>
                    @endif
                </div>
            </div>

            {{-- BOTTOM FULL WIDTH: LEAD HISTORY --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-history me-2 text-secondary"></i>Lead History
                            </h6>

                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager' && $histories->count() > 0)
                                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                    <i class="fas fa-trash me-1"></i> Bulk Delete
                                </button>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            @if($histories->count() > 0)

                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                <div class="px-3 py-2 border-bottom bg-light d-flex align-items-center gap-2">
                                    <input type="checkbox" id="selectAll" class="form-check-input mt-0">
                                    <label for="selectAll" class="mb-0 small text-muted">Select all</label>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                    <th style="width:50px;">Select</th>
                                                @endif
                                                <th>Status</th>
                                                <th>Next Follow Up Date</th>
                                                <th>Entered By</th>
                                                <th>Comments</th>
                                                <th>Entry / Created Date</th>
                                                <th style="width:90px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($histories as $history)
                                                <tr>
                                                    @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input record-checkbox" value="{{ $history->id }}">
                                                        </td>
                                                    @endif

                                                    <td>
                                                        <span class="badge px-2 py-1" style="
                                                            @php
                                                                $statusColors = [
                                                                    'In Measurement' => 'background:#fff3cd;color:#856404;',
                                                                    'Measurement Done' => 'background:#cff4fc;color:#055160;',
                                                                    'In Design' => 'background:#cfe2ff;color:#084298;',
                                                                    'Quotation Sent' => 'background:#e2e3e5;color:#41464b;',
                                                                    'Lead Rejected' => 'background:#f8d7da;color:#842029;',
                                                                    'Quotation Approved' => 'background:#d1e7dd;color:#0f5132;',
                                                                    'Advance Received' => 'background:#d1e7dd;color:#0a3622;',
                                                                    'Production Accepted' => 'background:#fff3cd;color:#664d03;',
                                                                    'Ready to Dispatched' => 'background:#cff4fc;color:#055160;',
                                                                    'Dispatched' => 'background:#cfe2ff;color:#084298;',
                                                                    'Dispatched Done' => 'background:#d1e7dd;color:#0f5132;',
                                                                    'Fitting Pending' => 'background:#fff3cd;color:#856404;',
                                                                    'Fitting Done' => 'background:#d1e7dd;color:#0a3622;',
                                                                ];
                                                                echo $statusColors[$history->iStatus] ?? 'background:#e2e3e5;color:#41464b;';
                                                            @endphp
                                                        ">
                                                            {{ $history->iStatus }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        {{ date('d-M-Y',strtotime($history->NetFolloupwdate)) ?: '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $history->user->full_name ?? ($history->user->strUserName ?? '—') }}
                                                    </td>

                                                    <td style="min-width: 260px;">
                                                        {{ $history->strComments ?: '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $history->EntryDate ? \Carbon\Carbon::parse($history->EntryDate)->format('d-M-Y') : '—' }}
                                                    </td>

                                                    <td class="text-center">
                                                        @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                            <a href="javascript:void(0);"
                                                               class="text-danger delete-single-record"
                                                               data-id="{{ $history->id }}"
                                                               title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>

                                                            <form id="delete-form-{{ $history->id }}"
                                                                  action="{{ route('store.leads.histories.delete', array($lead->iLeadId, $history->id)) }}"
                                                                  method="POST"
                                                                  style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="px-3 py-3">
                                    {{ $histories->links() }}
                                </div>
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

const statusHints = {
    'In Measurement'      : 'Enter the Measurement Visit Date (used as next follow-up).',
    'In Design'           : 'Enter the design follow-up date.',
    'Quotation Sent'      : 'Enter the date for quotation follow-up.',
    'Quotation Approved'  : 'Enter the advance payment follow-up date.',
    'Advance Received'    : 'Enter the expected production / follow-up date.',
    'Production Accepted' : 'Enter the expected dispatch date.',
    'Ready to Dispatched' : 'Enter the dispatch date.',
    'Dispatched'          : 'Enter the fitting schedule date.',
    'Fitting Pending'     : 'Enter the fitting date.',
};

const statusSelect = document.getElementById('iStatus');
const followupWrapper = document.getElementById('followupDateWrapper');
const followupInput = document.getElementById('NetFolloupwdate');
const followupReqStar = document.getElementById('followupRequired');
const followupHint = document.getElementById('followupHint');

function updateFollowupField() {
    const selected = statusSelect ? statusSelect.value : '';
    const isRequired = followupRequiredStatuses.includes(selected);

    if (followupWrapper) {
        followupWrapper.style.display = selected ? 'block' : 'none';
    }

    if (followupReqStar) {
        followupReqStar.style.display = isRequired ? 'inline' : 'none';
    }

    if (followupInput) {
        followupInput.required = isRequired;
    }

    if (followupHint) {
        followupHint.textContent = statusHints[selected] || '';
    }
}

if (statusSelect) {
    statusSelect.addEventListener('change', updateFollowupField);
    updateFollowupField();
}

$(document).ready(function () {
    $('#selectAll').on('change', function () {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.delete-single-record').on('click', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this history entry?')) {
            $('#delete-form-' + id).submit();
        }
    });

    $('#bulkDeleteBtn').on('click', function () {
        const ids = [];

        $('.record-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete ' + ids.length + ' selected record(s)?')) {
            $.ajax({
                url: "{{ route('store.leads.histories.bulk-delete', array($lead->iLeadId)) }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message || 'Unable to delete records.');
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        }
    });
});
</script>
@endsection