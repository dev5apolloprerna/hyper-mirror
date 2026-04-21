@extends('layouts.app')

@section('title', 'Edit Lead')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @include('common.alert')

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Edit Lead ({{ $lead->strLeadNo }})</h4>
                            <div class="page-title-right">
                                <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('store.leads.update', $lead->iLeadId) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Lead Number</label>
                                    <input type="text" class="form-control" value="{{ $lead->strLeadNo }}" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" class="form-control" value="{{ $lead->customer->strMobile ?? '' }}" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Customer Name <span style="color:red;">*</span></label>
                                    <input type="text" name="strCustomer" class="form-control"
                                        value="{{ old('strCustomer', $lead->customer->strCustomer ?? '') }}">
                                    @error('strCustomer')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Customer Type <span style="color:red;">*</span></label>
                                    <select name="customer_type" id="customer_type" class="form-control">
                                        <option value="Retail" {{ old('customer_type', $lead->customer->customer_type ?? 'Retail') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="B2B" {{ old('customer_type', $lead->customer->customer_type ?? '') == 'B2B' ? 'selected' : '' }}>B2B</option>
                                    </select>
                                    @error('customer_type')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control"
                                        value="{{ old('company_name', $lead->customer->company_name ?? '') }}">
                                    @error('company_name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Address</label>
                                    <textarea name="strAddress" class="form-control" rows="2">{{ old('strAddress', $lead->customer->strAddress ?? '') }}</textarea>
                                    @error('strAddress')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Site Address</label>
                                    <textarea name="SiteAddress" class="form-control" rows="2">{{ old('SiteAddress', $lead->SiteAddress) }}</textarea>
                                    @error('SiteAddress')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Is Only Fitting Quotation? <span style="color:red;">*</span></label>
                                    <select name="IsOnlyFittingQuotation" id="IsOnlyFittingQuotation" class="form-control">
                                        <option value="0" {{ old('IsOnlyFittingQuotation', (int)($lead->isFittingLeadOnly ?? 0)) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('IsOnlyFittingQuotation', (int)($lead->isFittingLeadOnly ?? 0)) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <!-- <div class="col-md-4 mb-4 fitting-required-box">
                                    <label class="form-label">Fitting Required?</label>
                                    <select name="isFittingRequired" id="isFittingRequired" class="form-control">
                                        <option value="0" {{ old('isFittingRequired', (int)($lead->isFittingRequired ?? 0)) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('isFittingRequired', (int)($lead->isFittingRequired ?? 0)) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div> -->

                                <div class="col-md-4 mb-4 fitting-charge-type-box">
                                    <label class="form-label">Fitting Charges Included or Extra?</label>
                                    <select name="isFittingChargeIncluded" id="isFittingChargeIncluded" class="form-control">
                                        <option value="">Select Option</option>
                                        <option value="0" {{ old('isFittingChargeIncluded', (int)($lead->isFittingChargeIncluded ?? 0)) === 0 ? 'selected' : '' }}>Extra</option>
                                        <option value="1" {{ old('isFittingChargeIncluded', (int)($lead->isFittingChargeIncluded ?? 0)) === 1 ? 'selected' : '' }}>Included</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4 measurement-required-box">
                                    <label class="form-label">Measurement Required</label>
                                    <select name="IsMeasureMentRequired" id="IsMeasureMentRequired" class="form-control">
                                        <option value="0" {{ old('IsMeasureMentRequired', (int)$lead->IsMeasureMentRequired) == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('IsMeasureMentRequired', (int)$lead->IsMeasureMentRequired) == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4 measurement-date-box">
                                    <label class="form-label">Measurement Visit Date</label>
                                    <input type="date" name="MeasurementVisitDate" class="form-control"
                                        value="{{ old('MeasurementVisitDate', $lead->MeasurementVisitDate) }}">
                                </div>

                                <div class="col-md-6 mb-4 quotation-date-box">
                                    <label class="form-label">In Design Follow Up Date</label>
                                    <input type="date" name="design_followup_date" class="form-control"
                                        value="{{ old('design_followup_date', $lead->NetFollowupdate) }}">
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Comment</label>
                                    <textarea name="strComments" class="form-control" rows="3" placeholder="Enter update comment">{{ old('strComments') }}</textarea>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Update Lead</button>
                                    <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function toggleLeadFields() {
                let onlyFitting = $('#IsOnlyFittingQuotation').val();
                //let fittingRequired = $('#isFittingRequired').val();
                let measurementRequired = $('#IsMeasureMentRequired').val();

                if (onlyFitting == '1') {
                    //$('.fitting-required-box').hide();
                    $('.fitting-charge-type-box').show();
                    $('.measurement-required-box').hide();
                    $('.measurement-date-box').hide();
                    $('.quotation-date-box').show();
                    //$('#isFittingRequired').val('1');
                } else {
                    //$('.fitting-required-box').show();
                    $('.measurement-required-box').show();
                    //$('.fitting-charge-type-box').toggle(fittingRequired == '1');
                    $('.fitting-charge-type-box').hide();
                    $('#isFittingChargeIncluded').val('');
                    $('.measurement-date-box').toggle(measurementRequired == '1');
                    $('.quotation-date-box').toggle(measurementRequired != '1');
                }
            }

            /*$('#IsOnlyFittingQuotation, #isFittingRequired, #IsMeasureMentRequired').on('change', toggleLeadFields);*/
             $('#IsOnlyFittingQuotation, #IsMeasureMentRequired').on('change', toggleLeadFields);
            toggleLeadFields();
        });
    </script>
@endsection
