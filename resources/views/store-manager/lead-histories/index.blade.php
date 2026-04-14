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
                                {{-- @if($lead->NetFollowupdate && !in_array($lead->iCurrentLeadStatus, ['Lead Rejected', 'Deal Done', 'Measurement Done'])) --}}
                                @if($lead->NetFollowupdate && !in_array($lead->iCurrentLeadStatus, ['Lead Rejected', 'Deal Done', 'Measurement Done', 'Ready to Dispatched']))
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
                                    <i class="fas fa-plus-circle"></i> Create Quotation                                
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

                            {{-- Delivery Challan for dispatch role --}}
                            @if($roleSlug === 'dispatch' && $lead->quotation)
                                <a href="{{ route('store.leads.delivery-challan', $lead->iLeadId) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-truck"></i> Delivery Challan
                                </a>
                            @endif
                             <!--  @if($roleSlug === 'production' && $lead->designs->count())
                                <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-outline-info btn-sm" title="View Design">
                                    <i class="fas fa-eye"></i> View Design
                                </a>
                            @endif -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOP SECTION --}}
            <div class="row g-3">

                {{-- LEFT: Lead Summary (printable) --}}
                <div class="col-xl-7 col-lg-7" id="leadSummarySection">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold d-flex align-items-center flex-wrap gap-2">
                                <span><i class="fas fa-info-circle me-2 text-primary"></i>Lead Summary</span>
                                <span class="badge bg-light text-dark border">
                                    Sales Person:
                                    {{ optional($lead->createdBy)->full_name ?: (optional($lead->createdBy)->name ?? (optional($lead->createdBy)->strUserName ?? '—')) }}
                                    @if(!empty(optional($lead->createdBy)->mobile_number) || !empty(optional($lead->createdBy)->strUserMobile))
                                        · {{ optional($lead->createdBy)->mobile_number ?? optional($lead->createdBy)->strUserMobile }}
                                    @endif
                                </span>
                            </h6>

                            @if($roleSlug === 'measurement')
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        onclick="printLeadSummary()"
                                        title="Print Lead Summary">
                                    <i class="fas fa-print me-1"></i> Print Summary
                                </button>
                            @endif
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
                                            <td class="text-muted">Address</td>
                                            <td>{{ $lead->customer->strAddress ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Site Address</td>
                                            <td>{{ $lead->SiteAddress ?? '—' }}</td>
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
                                        <tr>
                                            <td class="text-muted">Only Fitting Quotation</td>
                                            <td>{{ (int) ($lead->isFittingLeadOnly ?? 0) === 1 ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Fitting Required</td>
                                            <td>{{ (int) ($lead->isFittingRequired ?? 0) === 1 ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Fitting Charge Type</td>
                                            <td>
                                                @if((int) ($lead->isFittingRequired ?? 0) !== 1)
                                                    N/A
                                                @else
                                                    {{ (int) ($lead->isFittingChargeIncluded ?? 0) === 1 ? 'Included' : 'Extra' }}
                                                @endif
                                            </td>
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
                                        {{-- @if($lead->NetFollowupdate && !in_array($lead->iCurrentLeadStatus, [\App\Support\LeadWorkflow::STATUS_LEAD_REJECTED, \App\Support\LeadWorkflow::STATUS_DEAL_DONE, \App\Support\LeadWorkflow::STATUS_MEASUREMENT_DONE])) --}}
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

                                        @if($canViewFinancial)
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
                                        @endif

                                        @if(in_array($roleSlug, ['storemanager', 'account']) && $canViewFinancial)
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

                                        <tr>
                                            <td class="text-muted">Lead No</td>
                                            <td><strong>{{ $lead->strLeadNo }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Created Date</td>
                                            <td>{{ \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') }}</td>
                                        </tr>
                                        @if($lead->expected_delivery_date)
                                            <tr>
                                                <td class="text-muted">Expected Delivery</td>
                                                <td>{{ \Carbon\Carbon::parse($lead->expected_delivery_date)->format('d-m-Y') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            {{-- Quotation items in summary --}}
                            @if($lead->quotations && $lead->quotations->count())
                                <div class="mt-3 pt-3 border-top">
                                    <h6 class="fw-semibold mb-2">Quotation Items</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Shape</th>
                                                    <th>Unit</th>
                                                    <th>Qty</th>
                                                    <th>H × W</th>
                                                    @if(in_array($roleSlug, ['storemanager', 'account']) && $canViewFinancial)
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $activeBatchId = optional($lead->quotation)->quotation_batch_id;
                                                    $activeQuotations = $activeBatchId
                                                        ? $lead->quotations->where('quotation_batch_id', $activeBatchId)->values()
                                                        : $lead->quotations;
                                                @endphp

                                                @foreach($activeQuotations as $i => $q)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ optional($q->product)->strProductName ?? '—' }}</td>
                                                        <td>{{ optional($q->shape)->shape_title ?? '—' }}</td>
                                                        <td>{{ $q->unit_of_measurement ?? '—' }}</td>
                                                        <td>{{ $q->quantity ?? 1 }}</td>
                                                        <td>{{ $q->decHeight }} × {{ $q->decWidth }}</td>
                                                        @if(in_array($roleSlug, ['storemanager', 'account']) && $canViewFinancial)
                                                            <td>₹{{ number_format((float)$q->decRatePerSqft, 2) }}</td>
                                                            <td>₹{{ number_format((float)$q->iAmount, 2) }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                            @if(in_array($roleSlug, ['storemanager', 'account']) && $canViewFinancial)
                                             @php
                                                    $subtotalAmount = (float) $activeQuotations->sum('iAmount');
                                                    $fittingCharges = (float) ($lead->iFittingCharges ?? 0);
                                                    $discountAmount = ((int) ($lead->isDiscountApplicable ?? 0) === 1) ? (float) ($lead->decDiscountAmount ?? 0) : 0;
                                                    $amountAfterDiscount = max(($subtotalAmount + $fittingCharges) - $discountAmount, 0);
                                                    $gstAmount = ((int) ($lead->isGstApplicable ?? 0) === 1) ? (float) ($lead->decGstAmount ?? 0) : 0;
                                                @endphp

                                                <tfoot>
                                                    <tr>
                                                        <th colspan="7" class="text-end">Subtotal</th>
                                                        <th>₹{{ number_format($subtotalAmount, 2) }}</th>
                                                    </tr>
                                                    @if($fittingCharges > 0)
                                                        <tr>
                                                            <th colspan="7" class="text-end">Fitting Charges</th>
                                                            <th>₹{{ number_format($fittingCharges, 2) }}</th>
                                                        </tr>
                                                    @endif
                                                    @if((int) ($lead->isDiscountApplicable ?? 0) === 1 && $discountAmount > 0)
                                                        <tr>
                                                            <th colspan="7" class="text-end">Discount</th>
                                                            <th>- ₹{{ number_format($discountAmount, 2) }}</th>
                                                        </tr>
                                                    @endif
                                                    @if((int) ($lead->isGstApplicable ?? 0) === 1 && $gstAmount > 0)
                                                        <tr>
                                                            <th colspan="7" class="text-end">GST (18%)</th>
                                                            <th>₹{{ number_format($gstAmount, 2) }}</th>
                                                        </tr>
                                                    @endif

                                                    <tr>
                                                        <th colspan="7" class="text-end">Total</th>
                                                        <th>₹{{ number_format((float)($lead->iLeadAmount ?? ($amountAfterDiscount + $gstAmount)), 2) }}</th>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            @endif

                             @if($lead->designs && $lead->designs->count())
                                <div class="mt-3 pt-3 border-top">
                                    <h6 class="fw-semibold mb-2">Uploaded Designs</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:60px">#</th>
                                                    <th>Title</th>
                                                    <th style="width:120px">View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lead->designs as $index => $design)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $design->strTitle ?: 'Design File' }}</td>
                                                        <td>
                                                            <a href="{{ asset('uploads/lead-designs/' . $design->strFilename) }}"
                                                               target="_blank"
                                                               class="btn btn-outline-info btn-sm"
                                                               title="View Design">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

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

                        @if($lead->iCurrentLeadStatus === \App\Support\LeadWorkflow::STATUS_DEAL_DONE && in_array($roleSlug, ['storemanager', 'account']) && $canViewFinancial)
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
                                    {{-- @php $totalPaid = $lead->payments()->sum('iPaidAmount'); @endphp --}}
                                    <div class="alert alert-warning py-2 small mb-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <strong>Deal Done</strong> is not available yet.
                                        @if($canViewFinancial)
                                            @php $totalPaid = $lead->payments()->sum('iPaidAmount'); @endphp
                                            <br>
                                            Paid: ₹{{ number_format((float)$totalPaid, 2) }} /
                                            Required: ₹{{ number_format((float)$lead->iLeadAmount, 2) }}
                                        @endif
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
                                                    @if($status === \App\Support\LeadWorkflow::STATUS_DISPATCHED_DONE)
                                                        Dispatched To Client (Dispatched Done)
                                                    @elseif($status === \App\Support\LeadWorkflow::STATUS_RECEIVED_AT_NAROL)
                                                        Dispatched to Narol
                                                    @else
                                                        {{ $status }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('iStatus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Rejection Reason --}}
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

                                    {{-- Follow-up Date --}}
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

            {{-- BOTTOM: LEAD HISTORY --}}
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
                                                                'Received @ Narol'    => 'background:#e2d9f3;color:#432874;',
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

{{-- ═══════════════════════════════════════════════════════════
     PRINT STYLES — only shown when printing lead summary
══════════════════════════════════════════════════════════════ --}}
<style id="printStyles">
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
    }
    .no-print { display: none !important; }
    .badge { border: 1px solid #ccc !important; }
}
</style>

{{-- Hidden printable area --}}
<div id="printArea" style="display:none;">
    <div style="font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; padding: 20px;">
        <div style="text-align:center; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 20px;">
            <h2 style="margin:0; font-size:20px;">{{ config('app.name', 'Mirror CRM') }}</h2>
            <p style="margin:4px 0 0; color:#64748b; font-size:12px;">Lead Summary — {{ $lead->strLeadNo }}</p>
            <p style="margin:2px 0 0; color:#64748b; font-size:11px;">Printed on: {{ now()->format('d-m-Y H:i A') }}</p>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:20px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><td style="color:#64748b; padding:4px 0; width:45%;">Lead No</td><td style="font-weight:600;">{{ $lead->strLeadNo }}</td></tr>
                        <tr><td style="color:#64748b; padding:4px 0;">Customer</td><td style="font-weight:600;">{{ $lead->customer->strCustomer ?? '—' }}</td></tr>
                        <tr><td style="color:#64748b; padding:4px 0;">Mobile</td><td>{{ $lead->customer->strMobile ?? '—' }}</td></tr>
                        <tr><td style="color:#64748b; padding:4px 0;">Address</td><td>{{ $lead->customer->strAddress ?? '—' }}</td></tr>
                        <tr><td style="color:#64748b; padding:4px 0;">Site Address</td><td>{{ $lead->SiteAddress ?? '—' }}</td></tr>
                        <tr><td style="color:#64748b; padding:4px 0;">Current Status</td><td><strong>{{ $lead->iCurrentLeadStatus }}</strong></td></tr>
                    </table>
                </td>
                <td style="width:50%; vertical-align:top; padding-left:20px; border-left:1px dashed #cbd5e1;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr><td style="color:#64748b; padding:4px 0; width:50%;">Measurement Required</td><td>{{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</td></tr>
                        @if($lead->MeasurementVisitDate)
                            <tr><td style="color:#64748b; padding:4px 0;">Measurement Date</td><td>{{ \Carbon\Carbon::parse($lead->MeasurementVisitDate)->format('d-m-Y') }}</td></tr>
                        @endif
                        @if($lead->NetFollowupdate && $lead->iCurrentLeadStatus !== \App\Support\LeadWorkflow::STATUS_READY_TO_DISPATCHED)
                            <tr><td style="color:#64748b; padding:4px 0;">Next Follow Up</td><td>{{ \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') }}</td></tr>
                        @endif
                        @if($lead->expected_delivery_date)
                            <tr><td style="color:#64748b; padding:4px 0;">Expected Delivery</td><td>{{ \Carbon\Carbon::parse($lead->expected_delivery_date)->format('d-m-Y') }}</td></tr>
                        @endif
                        @if($canViewFinancial)
                            <tr><td style="color:#64748b; padding:4px 0;">Lead Amount</td><td><strong>₹{{ number_format((float)$lead->iLeadAmount, 2) }}</strong></td></tr>
                            @if($lead->iFittingCharges)
                                <tr><td style="color:#64748b; padding:4px 0;">Fitting Charges</td><td>₹{{ number_format((float)$lead->iFittingCharges, 2) }}</td></tr>
                            @endif
                        @endif
                        <tr><td style="color:#64748b; padding:4px 0;">Created Date</td><td>{{ \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Quotation items --}}
        @if($lead->quotations && $lead->quotations->count())
            @php
                $activeBatchId2 = optional($lead->quotation)->quotation_batch_id;
                $printQuotations = $activeBatchId2
                    ? $lead->quotations->where('quotation_batch_id', $activeBatchId2)->values()
                    : $lead->quotations;
            @endphp
            <div style="margin-top:16px;">
                <h4 style="font-size:14px; border-bottom:1px solid #cbd5e1; padding-bottom:6px; margin-bottom:10px;">Quotation Items</h4>
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="border:1px solid #cbd5e1; padding:6px;">#</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">Product</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">Category</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">Shape</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">Unit</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">Qty</th>
                            <th style="border:1px solid #cbd5e1; padding:6px;">H × W</th>
                            @if($canViewFinancial)
                                <th style="border:1px solid #cbd5e1; padding:6px;">Rate/Sqft</th>
                                <th style="border:1px solid #cbd5e1; padding:6px;">Amount</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($printQuotations as $i => $q)
                            <tr>
                                <td style="border:1px solid #cbd5e1; padding:6px; text-align:center;">{{ $i + 1 }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px;">{{ optional($q->product)->strProductName ?? '—' }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px;">{{ optional($q->category)->strCategoryName ?? '—' }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px;">{{ optional($q->shape)->shape_title ?? '—' }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px; text-align:center;">{{ $q->unit_of_measurement ?? '—' }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px; text-align:center;">{{ $q->quantity ?? 1 }}</td>
                                <td style="border:1px solid #cbd5e1; padding:6px; text-align:center;">{{ $q->decHeight }} × {{ $q->decWidth }}</td>
                                @if($canViewFinancial)
                                    <td style="border:1px solid #cbd5e1; padding:6px; text-align:right;">₹{{ number_format((float)$q->decRatePerSqft, 2) }}</td>
                                    <td style="border:1px solid #cbd5e1; padding:6px; text-align:right;">₹{{ number_format((float)$q->iAmount, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    @if($canViewFinancial)
                        <tfoot>
                            <tr style="background:#f8fafc; font-weight:700;">
                                <td colspan="8" style="border:1px solid #cbd5e1; padding:6px; text-align:right;">Grand Total</td>
                                <td style="border:1px solid #cbd5e1; padding:6px; text-align:right;">₹{{ number_format((float)$lead->iLeadAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif

        <div style="margin-top:50px; display:flex; justify-content:space-between;">
            <div style="text-align:center;">
                <div style="border-top:1px solid #334155; width:180px; margin-bottom:4px;"></div>
                <p style="font-size:11px; color:#64748b;">Customer Signature</p>
            </div>
            <div style="text-align:center;">
                <div style="border-top:1px solid #334155; width:180px; margin-bottom:4px;"></div>
                <p style="font-size:11px; color:#64748b;">Authorised Signature</p>
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
    'Ready to Dispatched',
    'Received @ Narol',
];

const statusHints = {
    'In Measurement'      : 'Enter the measurement visit date.',
    'In Design'           : 'Enter the design follow-up date.',
    'Quotation Sent'      : 'Enter the quotation follow-up date.',
    'Quotation Approved'  : 'Enter the advance payment follow-up date.',
    'Advance Received'    : 'Enter the expected production / follow-up date.',
    'Production Accepted' : 'Enter the expected dispatch date.',
    'Dispatched'          : 'Choose whether dispatch is completed to client or sent to Narol.',
    'Received @ Narol'    : 'No follow-up is required for this status.',
    'Fitting Pending'     : 'Enter the fitting date.',
};

const statusSelect            = document.getElementById('iStatus');
const followupWrapper         = document.getElementById('followupDateWrapper');
const followupInput           = document.getElementById('NetFolloupwdate');
const followupReqStar         = document.getElementById('followupRequired');
const followupHint            = document.getElementById('followupHint');
const rejectionWrapper        = document.getElementById('rejectionReasonWrapper');
const rejectionInput          = document.getElementById('rejection_reason');
const expectedDeliveryWrapper = document.getElementById('expectedDeliveryWrapper');
const expectedDeliveryInput   = document.getElementById('expected_delivery_date');

function updateFormFields() {
    if (!statusSelect) return;

    const selected            = statusSelect.value;
    const isReject            = selected === 'Lead Rejected';
    const isDealDone          = selected === 'Deal Done';
    const isQuotationApproved = selected === 'Quotation Approved';
    const noFollowup          = NO_FOLLOWUP_STATUSES.includes(selected) || !selected;
    const isRequired          = followupRequiredStatuses.includes(selected);

    if (rejectionWrapper) {
        rejectionWrapper.style.display = isReject ? 'block' : 'none';
        if (rejectionInput) rejectionInput.required = isReject;
    }

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

// ── Print Lead Summary ────────────────────────────────────────────────────────
function printLeadSummary() {
    const printContent = document.getElementById('printArea').innerHTML;
    const originalBody = document.body.innerHTML;

    document.body.innerHTML = `
        <html>
            <head>
                <title>Lead Summary — {{ $lead->strLeadNo }}</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; margin: 0; padding: 0; }
                    table { border-collapse: collapse; }
                    @page { margin: 15mm; }
                </style>
            </head>
            <body>${printContent}</body>
        </html>`;

    window.print();
    document.body.innerHTML = originalBody;
    window.location.reload();
}
</script>
@endsection