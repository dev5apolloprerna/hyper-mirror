@extends('layouts.app')

@section('title', 'Add Lead')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @include('common.alert')

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Add Lead</h4>
                            <div class="page-title-right">
                                <a href="{{ route('store.leads.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('store.leads.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Mobile <span style="color:red;">*</span></label>
                                    <input type="text" name="strMobile" id="strMobile" class="form-control"
                                        value="{{ old('strMobile') }}" maxlength="10" placeholder="Enter mobile number">
                                    @if ($errors->has('strMobile'))
                                        <span class="text-danger">{{ $errors->first('strMobile') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-info btn-sm" id="checkCustomerBtn">
                                        Check Customer
                                    </button>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Customer Name <span style="color:red;">*</span></label>
                                    <input type="text" name="strCustomer" id="strCustomer" class="form-control"
                                        value="{{ old('strCustomer') }}" placeholder="Enter customer name">
                                    @if ($errors->has('strCustomer'))
                                        <span class="text-danger">{{ $errors->first('strCustomer') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Customer Type <span style="color:red;">*</span></label>
                                    <select name="customer_type" id="customer_type" class="form-control">
                                        <option value="Retail"
                                            {{ old('customer_type', 'Retail') == 'Retail' ? 'selected' : '' }}>Retail
                                        </option>
                                        <option value="B2B" {{ old('customer_type') == 'B2B' ? 'selected' : '' }}>B2B
                                        </option>
                                    </select>
                                    @if ($errors->has('customer_type'))
                                        <span class="text-danger">{{ $errors->first('customer_type') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Company Name (Optional)</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control"
                                        value="{{ old('company_name') }}"
                                        placeholder="Enter company name for B2B customers">
                                    @if ($errors->has('company_name'))
                                        <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Address</label>
                                    <textarea name="strAddress" id="strAddress" class="form-control" rows="2" placeholder="Enter address">{{ old('strAddress') }}</textarea>
                                    @if ($errors->has('strAddress'))
                                        <span class="text-danger">{{ $errors->first('strAddress') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" id="sameAddress" class="form-check-input">
                                        <label class="form-check-label" for="sameAddress">
                                            Site Address Same as Above
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Site Address</label>
                                    <textarea name="SiteAddress" id="SiteAddress" class="form-control" rows="3" placeholder="Enter site address">{{ old('SiteAddress') }}</textarea>
                                    @if ($errors->has('SiteAddress'))
                                        <span class="text-danger">{{ $errors->first('SiteAddress') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Is Only Fitting Quotation? <span
                                            style="color:red;">*</span></label>
                                    <select name="IsOnlyFittingQuotation" id="IsOnlyFittingQuotation" class="form-control">
                                        <option value="0"
                                            {{ old('IsOnlyFittingQuotation') == '0' ? 'selected' : '' }}>No</option>
                                        <option value="1"
                                            {{ old('IsOnlyFittingQuotation') == '1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                    @if ($errors->has('IsOnlyFittingQuotation'))
                                        <span class="text-danger">{{ $errors->first('IsOnlyFittingQuotation') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-4 fitting-required-box">
                                    <label class="form-label">Fitting Required? <span style="color:red;">*</span></label>
                                    <select name="isFittingRequired" id="isFittingRequired" class="form-control">
                                        <option value="0" {{ old('isFittingRequired') == '0' ? 'selected' : '' }}>No
                                        </option>
                                        <option value="1" {{ old('isFittingRequired') == '1' ? 'selected' : '' }}>Yes
                                        </option>
                                    </select>
                                    @if ($errors->has('isFittingRequired'))
                                        <span class="text-danger">{{ $errors->first('isFittingRequired') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4 mb-4 fitting-charge-type-box">
                                    <label class="form-label">Fitting Charges Included or Extra? <span
                                            style="color:red;">*</span></label>
                                    <select name="isFittingChargeIncluded" id="isFittingChargeIncluded"
                                        class="form-control">
                                        <option value="">Select Option</option>
                                        <option value="0"
                                            {{ old('isFittingChargeIncluded') == '0' ? 'selected' : '' }}>Extra</option>
                                        <option value="1"
                                            {{ old('isFittingChargeIncluded') == '1' ? 'selected' : '' }}>Included</option>
                                    </select>
                                    @if ($errors->has('isFittingChargeIncluded'))
                                        <span class="text-danger">{{ $errors->first('isFittingChargeIncluded') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4 measurement-required-box">
                                    <label class="form-label">Measurement Required <span
                                            style="color:red;">*</span></label>
                                    <select name="IsMeasureMentRequired" id="IsMeasureMentRequired" class="form-control">
                                        <option value="0"
                                            {{ old('IsMeasureMentRequired') == '0' ? 'selected' : '' }}>No</option>
                                        <option value="1"
                                            {{ old('IsMeasureMentRequired') == '1' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                    @if ($errors->has('IsMeasureMentRequired'))
                                        <span class="text-danger">{{ $errors->first('IsMeasureMentRequired') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4 measurement-date-box">
                                    <label class="form-label">Measurement Visit Date</label>
                                    <input type="date" name="MeasurementVisitDate" class="form-control"
                                        value="{{ old('MeasurementVisitDate') }}">
                                    @if ($errors->has('MeasurementVisitDate'))
                                        <span class="text-danger">{{ $errors->first('MeasurementVisitDate') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4 quotation-date-box">
                                    <label class="form-label">In Design Follow Up Date</label>
                                    <input type="date" name="design_followup_date" class="form-control"
                                        value="{{ old('design_followup_date') }}">
                                    @if ($errors->has('design_followup_date'))
                                        <span class="text-danger">{{ $errors->first('design_followup_date') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Comment</label>
                                    <textarea name="strComments" class="form-control" rows="3" placeholder="Enter lead comment">{{ old('strComments') }}</textarea>
                                    @if ($errors->has('strComments'))
                                        <span class="text-danger">{{ $errors->first('strComments') }}</span>
                                    @endif
                                </div>


                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
                let fittingRequired = $('#isFittingRequired').val();
                let measurementRequired = $('#IsMeasureMentRequired').val();

                if (onlyFitting == '1') {
                    $('.fitting-required-box').hide();
                    $('.fitting-charge-type-box').show();
                    $('.measurement-required-box').hide();
                    $('.measurement-date-box').hide();
                    $('.quotation-date-box').show();

                    $('#isFittingRequired').val('1');
                } else {
                    $('.fitting-required-box').show();

                    if (fittingRequired == '1') {
                        $('.fitting-charge-type-box').show();
                    } else {
                        $('.fitting-charge-type-box').hide();
                        $('#isFittingChargeIncluded').val('');
                    }

                    $('.measurement-required-box').show();

                    if (measurementRequired == '1') {
                        $('.measurement-date-box').show();
                        $('.quotation-date-box').hide();
                    } else {
                        $('.measurement-date-box').hide();
                        $('.quotation-date-box').show();
                    }
                }
            }

            //  toggleLeadFields();

            toggleLeadFields();
            toggleCompanyField();


            /*$('#IsOnlyFittingQuotation, #isFittingRequired, #IsMeasureMentRequired').on('change', function () {
                toggleLeadFields();
            });*/

            $('#IsOnlyFittingQuotation, #isFittingRequired, #IsMeasureMentRequired').on('change', function() {
                toggleLeadFields();
            });

            function toggleCompanyField() {
                const isB2B = $('#customer_type').val() === 'B2B';
                $('#company_name').prop('required', false);
                if (!isB2B) {
                    $('#company_name').val('');
                }
            }

            $('#customer_type').on('change', toggleCompanyField);


            $('#checkCustomerBtn').on('click', function() {
                let mobile = $('#strMobile').val();

                if (mobile == '') {
                    alert('Please enter mobile number.');
                    return;
                }

                $.ajax({
                    url: "{{ route('store.leads.check-customer') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile: mobile
                    },
                    success: function(response) {
                        if (response) {
                            $('#strCustomer').val(response.strCustomer);
                            $('#strAddress').val(response.strAddress);
                            $('#customer_type').val(response.customer_type || 'Retail');
                            $('#company_name').val(response.company_name || '');
                            toggleCompanyField();

                        } else {
                            alert('Customer not found.');
                            $('#strCustomer').val('');
                            $('#strAddress').val('');
                        }
                    },
                    error: function() {
                        alert('Something went wrong.');
                    }
                });
            });
        });
    </script>

    {{-- address based same site address then copy address this logic --}}
    <script>
        document.getElementById('sameAddress').addEventListener('change', function() {
            let address = document.getElementById('strAddress');
            let siteAddress = document.getElementById('SiteAddress');

            if (this.checked) {
                siteAddress.value = address.value;
                siteAddress.setAttribute('readonly', true);
            } else {
                siteAddress.value = '';
                siteAddress.removeAttribute('readonly');
            }
        });

        document.getElementById('strAddress').addEventListener('input', function() {
            let checkbox = document.getElementById('sameAddress');
            let siteAddress = document.getElementById('SiteAddress');

            if (checkbox.checked) {
                siteAddress.value = this.value;
            }
        });
    </script>
@endsection
