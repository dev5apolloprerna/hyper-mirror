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
                                <input type="text" name="strMobile" id="strMobile" class="form-control" value="{{ old('strMobile') }}" maxlength="10" placeholder="Enter mobile number">
                                @if($errors->has('strMobile'))
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
                                <input type="text" name="strCustomer" id="strCustomer" class="form-control" value="{{ old('strCustomer') }}" placeholder="Enter customer name">
                                @if($errors->has('strCustomer'))
                                    <span class="text-danger">{{ $errors->first('strCustomer') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="strAddress" id="strAddress" class="form-control" rows="2" placeholder="Enter address">{{ old('strAddress') }}</textarea>
                                @if($errors->has('strAddress'))
                                    <span class="text-danger">{{ $errors->first('strAddress') }}</span>
                                @endif
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Site Address</label>
                                <textarea name="SiteAddress" class="form-control" rows="3" placeholder="Enter site address">{{ old('SiteAddress') }}</textarea>
                                @if($errors->has('SiteAddress'))
                                    <span class="text-danger">{{ $errors->first('SiteAddress') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Measurement Required <span style="color:red;">*</span></label>
                                <select name="IsMeasureMentRequired" id="IsMeasureMentRequired" class="form-control">
                                    <option value="0" {{ old('IsMeasureMentRequired') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('IsMeasureMentRequired') == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                                @if($errors->has('IsMeasureMentRequired'))
                                    <span class="text-danger">{{ $errors->first('IsMeasureMentRequired') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4 measurement-date-box">
                                <label class="form-label">Measurement Visit Date</label>
                                <input type="date" name="MeasurementVisitDate" class="form-control" value="{{ old('MeasurementVisitDate') }}">
                                @if($errors->has('MeasurementVisitDate'))
                                    <span class="text-danger">{{ $errors->first('MeasurementVisitDate') }}</span>
                                @endif
                            </div>

                             <div class="col-md-6 mb-4 quotation-date-box">
                                <label class="form-label">In Design Follow Up Date</label>
                                <input type="date" name="design_followup_date" class="form-control" value="{{ old('design_followup_date') }}">
                                @if($errors->has('design_followup_date'))
                                    <span class="text-danger">{{ $errors->first('design_followup_date') }}</span>
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
    $(document).ready(function () {
        function toggleDateFields() {
            let value = $('#IsMeasureMentRequired').val();

            if (value == '1') {
                $('.measurement-date-box').show();
                $('.quotation-date-box').hide();
            } else {
                $('.measurement-date-box').hide();
                $('.quotation-date-box').show();
            }
        }

        toggleDateFields();

        $('#IsMeasureMentRequired').on('change', function () {
            toggleDateFields();
        });

        $('#checkCustomerBtn').on('click', function () {
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
                success: function (response) {
                    if (response) {
                        $('#strCustomer').val(response.strCustomer);
                        $('#strAddress').val(response.strAddress);
                    } else {
                        alert('Customer not found.');
                        $('#strCustomer').val('');
                        $('#strAddress').val('');
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        });
    });
</script>
@endsection
