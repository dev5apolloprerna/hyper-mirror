@extends('layouts.app')
@section('title', 'Lead #' . $lead->strLeadNo)

@section('content')
<div class="row g-3">

    {{-- ===================================================
         LEFT: Lead Info + Status Change
    =================================================== --}}
    <div class="col-lg-7">

        {{-- Success / Error alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Lead summary card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $lead->strLeadNo }}</h6>
                    <small class="text-muted">{{ $lead->customer->strCustomer ?? '—' }} &bull; {{ $lead->customer->strMobile ?? '' }}</small>
                </div>
                <span class="badge {{ $lead->status_badge }} fs-6 px-3 py-2">
                    {{ $lead->status_label }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-2 small">
                    <div class="col-6">
                        <span class="text-muted">Showroom</span><br>
                        <strong>{{ $lead->showroom->strShowRoomName ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Created By</span><br>
                        <strong>{{ $lead->createdBy->full_name ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Measurement Required</span><br>
                        <strong>{{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted">Lead Amount</span><br>
                        <strong>₹{{ number_format($lead->iLeadAmount, 2) }}</strong>
                    </div>
                    @if($lead->MeasurementVisitDate)
                    <div class="col-6">
                        <span class="text-muted">Measurement Visit Date</span><br>
                        <strong>{{ $lead->MeasurementVisitDate->format('d M Y') }}</strong>
                    </div>
                    @endif
                    @if($lead->NetFollowupdate)
                    <div class="col-6">
                        <span class="text-muted">Next Followup</span><br>
                        <strong>{{ $lead->NetFollowupdate->format('d M Y') }}</strong>
                    </div>
                    @endif
                    @if($lead->DispatchedDate)
                    <div class="col-6">
                        <span class="text-muted">Dispatched Date</span><br>
                        <strong>{{ $lead->DispatchedDate->format('d M Y') }}</strong>
                    </div>
                    @endif
                    @if($lead->FittingDate)
                    <div class="col-6">
                        <span class="text-muted">Fitting Date</span><br>
                        <strong>{{ $lead->FittingDate->format('d M Y') }}</strong>
                    </div>
                    @endif
                    @if($lead->SiteAddress)
                    <div class="col-12">
                        <span class="text-muted">Site Address</span><br>
                        <strong>{{ $lead->SiteAddress }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================================================
             Status Change Form
             Only shown if there are valid next statuses
        =================================================== --}}
        @if(!empty($nextStatuses) && !$lead->isRejected())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fa fa-exchange-alt me-2 text-primary"></i>Change Status</h6>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('leads.updateStatus', $lead->iLeadId) }}"
                      id="statusChangeForm">
                    @csrf
                    @method('PATCH')

                    {{-- Status Dropdown --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="new_status">New Status <span class="text-danger">*</span></label>
                        <select name="new_status"
                                id="new_status"
                                class="form-select @error('new_status') is-invalid @enderror"
                                required>
                            <option value="">— Select new status —</option>
                            @foreach($nextStatuses as $statusId)
                                <option value="{{ $statusId }}"
                                    {{ old('new_status') == $statusId ? 'selected' : '' }}>
                                    {{ $statusLabels[$statusId] ?? $statusId }}
                                </option>
                            @endforeach
                        </select>
                        @error('new_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dynamic Date Field (shown/hidden + label changed by JS) --}}
                    <div class="mb-3" id="dateWrapper" style="display:none;">
                        <label class="form-label fw-semibold" for="status_date" id="dateLabel">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="status_date"
                               id="status_date"
                               class="form-control @error('status_date') is-invalid @enderror"
                               value="{{ old('status_date') }}"
                               min="{{ date('Y-m-d') }}">
                        @error('status_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Comment --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="strComments">
                            Comment <span class="text-danger">*</span>
                        </label>
                        <textarea name="strComments"
                                  id="strComments"
                                  rows="3"
                                  class="form-control @error('strComments') is-invalid @enderror"
                                  placeholder="Enter a comment for this status change..."
                                  required>{{ old('strComments') }}</textarea>
                        @error('strComments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-check me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
        @elseif($lead->isRejected())
            <div class="alert alert-danger">This lead has been rejected. No further status changes are possible.</div>
        @else
            <div class="alert alert-secondary">No status transitions available for your role at this stage.</div>
        @endif

    </div>

    {{-- ===================================================
         RIGHT: History Timeline
    =================================================== --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fa fa-history me-2 text-secondary"></i>Lead History</h6>
            </div>
            <div class="card-body p-0" style="max-height:600px; overflow-y:auto;">
                @forelse($histories as $history)
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="badge {{ \App\Enums\LeadStatus::badgeClass((int)$history->iStatus) }} small">
                            {{ $history->status_label }}
                        </span>
                        <small class="text-muted">{{ $history->EntryDate?->format('d M Y, h:i A') }}</small>
                    </div>
                    <p class="mb-1 small">{{ $history->strComments }}</p>
                    <div class="d-flex gap-3 small text-muted">
                        <span><i class="fa fa-user me-1"></i>{{ $history->enteredBy->full_name ?? '—' }}</span>
                        @if($history->NetFolloupwdate)
                            <span><i class="fa fa-calendar me-1"></i>{{ $history->NetFolloupwdate->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted small">No history recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Date configs from PHP — shape: { "8": { required: true, label: "Dispatched Date", field: "..." }, ... }
    const dateConfigs = {!! $dateConfigsJson !!};

    const statusSelect  = document.getElementById('new_status');
    const dateWrapper   = document.getElementById('dateWrapper');
    const dateLabel     = document.getElementById('dateLabel');
    const dateInput     = document.getElementById('status_date');

    function updateDateField() {
        const selectedStatus = statusSelect.value;
        const config = dateConfigs[selectedStatus];

        if (config && config.required) {
            dateWrapper.style.display = 'block';
            dateLabel.innerHTML = config.label + ' <span class="text-danger">*</span>';
            dateInput.required = true;
        } else {
            dateWrapper.style.display = 'none';
            dateInput.required = false;
            dateInput.value = '';
        }
    }

    statusSelect.addEventListener('change', updateDateField);

    // On page load — restore state if old() value exists (after validation failure)
    updateDateField();
</script>
@endpush
