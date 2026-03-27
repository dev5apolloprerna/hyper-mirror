@extends('layouts.app')

@section('title', 'Lead Workflow')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            {{-- Page Header --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="workflow-header-card">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <h4 class="mb-0 fw-bold text-dark">Lead Workflow</h4>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                        Lead No: {{ $lead->strLeadNo }}
                                    </span>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-2">
                                        Current Status: {{ $lead->iCurrentLeadStatus }}
                                    </span>
                                </div>
                                <p class="text-muted mb-0">
                                    Manage lead summary, follow-ups, customer discussions, and history in one place.
                                </p>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                @if($lead->quotation)
                                    <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}" class="btn btn-info btn-sm px-3">
                                        <i class="fas fa-file-invoice me-1"></i> Quotation
                                    </a>
                                @endif

                                @if(in_array(optional(auth()->user()->crmRole)->slug, ['storemanager', 'account']))
                                    <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="btn btn-dark btn-sm px-3">
                                        <i class="fas fa-money-bill-wave me-1"></i> Payments
                                    </a>
                                @endif

                                <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm px-3">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="summary-card summary-card-primary h-100">
                        <div class="summary-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="summary-label">Customer</div>
                            <div class="summary-value">{{ $lead->customer->strCustomer ?? '-' }}</div>
                            <div class="summary-subtext">{{ $lead->customer->strMobile ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-card summary-card-success h-100">
                        <div class="summary-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div>
                            <div class="summary-label">Lead Amount</div>
                            <div class="summary-value">₹ {{ number_format((float) $lead->iLeadAmount, 2) }}</div>
                            <div class="summary-subtext">Fitting: ₹ {{ number_format((float) $lead->iFittingCharges, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-card summary-card-warning h-100">
                        <div class="summary-icon">
                            <i class="fas fa-ruler-combined"></i>
                        </div>
                        <div>
                            <div class="summary-label">Measurement</div>
                            <div class="summary-value">
                                {{ $lead->IsMeasureMentRequired ? 'Required' : 'Not Required' }}
                            </div>
                            <div class="summary-subtext">
                                Date: {{ $lead->MeasurementVisitDate ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-card summary-card-info h-100">
                        <div class="summary-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="summary-label">Next Follow Up</div>
                            <div class="summary-value">{{ $lead->NetFollowupdate ?: '-' }}</div>
                            <div class="summary-subtext">Track next action clearly</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Section --}}
            <div class="row g-4 mb-4">
                {{-- Left Side: Lead Summary --}}
                <div class="col-xl-4">
                    <div class="card custom-card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h5 class="mb-0 fw-semibold">Lead Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-list">
                                <div class="info-item">
                                    <span class="info-title">Customer</span>
                                    <span class="info-text">{{ $lead->customer->strCustomer ?? '-' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Mobile</span>
                                    <span class="info-text">{{ $lead->customer->strMobile ?? '-' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Lead Amount</span>
                                    <span class="info-text">₹ {{ number_format((float) $lead->iLeadAmount, 2) }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Measurement Required</span>
                                    <span class="info-text">{{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Measurement Date</span>
                                    <span class="info-text">{{ $lead->MeasurementVisitDate ?: '-' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-title">Next Followup</span>
                                    <span class="info-text">{{ $lead->NetFollowupdate ?: '-' }}</span>
                                </div>
                                <div class="info-item border-0 pb-0 mb-0">
                                    <span class="info-title">Fitting Charges</span>
                                    <span class="info-text">₹ {{ number_format((float) $lead->iFittingCharges, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Form --}}
                <div class="col-xl-8">
                    @if(!empty($allowedStatuses))
                        <div class="card custom-card shadow-sm border-0 h-100">
                            <div class="card-header bg-white border-bottom-0 pb-0">
                                <h5 class="mb-0 fw-semibold">Add New Status / Discussion</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('store.leads.histories.store', $lead->iLeadId) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Next Status <span class="text-danger">*</span></label>
                                        <select name="iStatus" id="iStatus" class="form-select custom-input">
                                            <option value="">Select Status</option>
                                            @foreach($allowedStatuses as $status)
                                                <option value="{{ $status }}" {{ old('iStatus') === $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('iStatus')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Next Follow Up Date</label>
                                        <input
                                            type="date"
                                            name="NetFolloupwdate"
                                            id="NetFolloupwdate"
                                            class="form-control custom-input"
                                            value="{{ old('NetFolloupwdate') }}"
                                        >
                                        <small class="text-muted">Required for followup-based statuses.</small>
                                        @error('NetFolloupwdate')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Discussion / Comments <span class="text-danger">*</span></label>
                                        <textarea
                                            name="strComments"
                                            class="form-control custom-input"
                                            rows="8"
                                            placeholder="Enter discussion with customer, visit note, or status update"
                                        >{{ old('strComments') }}</textarea>
                                        @error('strComments')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 btn-save-discussion">
                                        <i class="fas fa-save me-1"></i> Save Discussion
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bottom Full Width: History --}}
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <div>
                                <h5 class="mb-0 fw-semibold">Lead Discussion History</h5>
                                <small class="text-muted">All previous lead updates, comments, and follow-up records.</small>
                            </div>

                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                <button type="button" class="btn btn-danger btn-sm px-3" id="bulkDeleteBtn">
                                    <i class="fas fa-trash me-1"></i> Bulk Delete
                                </button>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive custom-table-wrap">
                                <table class="table table-hover align-middle mb-0 custom-history-table">
                                    <thead>
                                        <tr>
                                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                <th width="5%">
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                            @endif
                                            <th>Status</th>
                                            <th>Next Follow Up Date</th>
                                            <th>Comments</th>
                                            <th>Entered By</th>
                                            <th>Entry Date</th>
                                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                <th width="8%">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($histories as $history)
                                            <tr>
                                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                    <td>
                                                        <input type="checkbox" class="record-checkbox" value="{{ $history->id }}">
                                                    </td>
                                                @endif

                                                <td>
                                                    <span class="status-pill">
                                                        {{ $history->iStatus }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="text-nowrap">
                                                        {{ $history->NetFolloupwdate ?: '-' }}
                                                    </span>
                                                </td>

                                                <td style="min-width: 250px;">
                                                    <div class="comment-box">
                                                        {{ $history->strComments }}
                                                    </div>
                                                </td>

                                                <td>
                                                    {{ $history->user->full_name ?: ($history->user->first_name ?? $history->user->email ?? '-') }}
                                                </td>

                                                <td>
                                                    <span class="text-nowrap">{{ $history->EntryDate }}</span>
                                                </td>

                                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                    <td>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-light-danger delete-record" data-id="{{ $history->id }}" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                        <form id="delete-form-{{ $history->id }}" action="{{ route('store.leads.histories.delete', [$lead->iLeadId, $history->id]) }}" method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ optional(auth()->user()->crmRole)->slug === 'storemanager' ? 7 : 5 }}" class="text-center py-5 text-muted">
                                                    No history found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($histories->hasPages())
                                <div class="p-3 border-top">
                                    <div class="d-flex justify-content-center">
                                        {{ $histories->links() }}
                                    </div>
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

@section('styles')
<style>
    .workflow-header-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid #e9edf5;
    }

    .custom-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border: 1px solid #edf1f7;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
        transition: all 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .summary-card-primary .summary-icon {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
    }

    .summary-card-success .summary-icon {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
    }

    .summary-card-warning .summary-icon {
        background: rgba(255, 193, 7, 0.16);
        color: #d39e00;
    }

    .summary-card-info .summary-icon {
        background: rgba(13, 202, 240, 0.14);
        color: #0dcaf0;
    }

    .summary-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .summary-value {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.3;
    }

    .summary-subtext {
        font-size: 13px;
        color: #7b8794;
        margin-top: 4px;
    }

    .info-list .info-item {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px dashed #e5e7eb;
    }

    .info-title {
        color: #6b7280;
        font-weight: 600;
        min-width: 140px;
    }

    .info-text {
        color: #111827;
        font-weight: 500;
        text-align: right;
    }

    .custom-input {
        border-radius: 10px;
        border: 1px solid #dbe3ef;
        padding: 10px 12px;
        box-shadow: none !important;
    }

    .custom-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.12) !important;
    }

    .btn-save-discussion {
        border-radius: 10px;
        padding: 11px 16px;
        font-weight: 600;
    }

    .custom-history-table thead th {
        background: #0b5aa2;
        color: #fff;
        border-color: #0b5aa2;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        vertical-align: middle;
    }

    .custom-history-table tbody td {
        vertical-align: middle;
        font-size: 14px;
        color: #374151;
    }

    .custom-history-table tbody tr:hover {
        background: #f8fbff;
    }

    .status-pill {
        display: inline-block;
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .comment-box {
        color: #374151;
        line-height: 1.5;
        white-space: normal;
        word-break: break-word;
    }

    .btn-light-danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-radius: 8px;
        border: none;
    }

    .btn-light-danger:hover {
        background: #dc3545;
        color: #fff;
    }

    .bg-primary-subtle {
        background: rgba(13, 110, 253, 0.12);
    }

    .bg-warning-subtle {
        background: rgba(255, 193, 7, 0.16);
    }

    .custom-table-wrap {
        overflow-x: auto;
    }

    @media (max-width: 1199.98px) {
        .info-text {
            text-align: left;
        }
    }

    @media (max-width: 991.98px) {
        .workflow-header-card {
            padding: 18px;
        }

        .summary-value {
            font-size: 18px;
        }

        .info-list .info-item {
            flex-direction: column;
            gap: 4px;
        }

        .info-text {
            text-align: left;
        }
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('#selectAll').on('click', function () {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.delete-record').on('click', function () {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this history?')) {
            $('#delete-form-' + id).submit();
        }
    });

    $('#bulkDeleteBtn').on('click', function () {
        let ids = [];
        $('.record-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete selected histories?')) {
            $.ajax({
                url: "{{ route('store.leads.histories.bulk-delete', $lead->iLeadId) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    });
});
</script>
@endsection