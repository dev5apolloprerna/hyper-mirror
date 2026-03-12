@extends('layouts.app')

@section('title', 'Lead Quotation')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Lead Quotation</h4>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5>Lead Details</h5>
                    <div class="row">
                        <div class="col-md-4"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</div>
                        <div class="col-md-4"><strong>Customer:</strong> {{ $lead->customer->strCustomer ?? '' }}</div>
                        <div class="col-md-4"><strong>Mobile:</strong> {{ $lead->customer->strMobile ?? '' }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.leads.save-quotation', $lead->iLeadId) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Product Category <span style="color:red;">*</span></label>
                                <select name="iProductCategoryId" id="iProductCategoryId" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->iCategoryId }}" {{ old('iProductCategoryId') == $category->iCategoryId ? 'selected' : '' }}>
                                            {{ $category->strCategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('iProductCategoryId'))
                                    <span class="text-danger">{{ $errors->first('iProductCategoryId') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Product <span style="color:red;">*</span></label>
                                <select name="iProductId" id="iProductId" class="form-control">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->iProductId }}"
                                                data-category="{{ $product->iCategoryId }}"
                                                {{ old('iProductId') == $product->iProductId ? 'selected' : '' }}>
                                            {{ $product->strProductName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('iProductId'))
                                    <span class="text-danger">{{ $errors->first('iProductId') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Height <span style="color:red;">*</span></label>
                                <input type="number" step="0.01" min="0" name="decHeight" id="decHeight" class="form-control" value="{{ old('decHeight') }}">
                                @if($errors->has('decHeight'))
                                    <span class="text-danger">{{ $errors->first('decHeight') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Width <span style="color:red;">*</span></label>
                                <input type="number" step="0.01" min="0" name="decWidth" id="decWidth" class="form-control" value="{{ old('decWidth') }}">
                                @if($errors->has('decWidth'))
                                    <span class="text-danger">{{ $errors->first('decWidth') }}</span>
                                @endif
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Rate Per Sqft <span style="color:red;">*</span></label>
                                <input type="number" step="0.01" min="0" name="decRatePerSqft" id="decRatePerSqft" class="form-control" value="{{ old('decRatePerSqft') }}">
                                @if($errors->has('decRatePerSqft'))
                                    <span class="text-danger">{{ $errors->first('decRatePerSqft') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Total Sqft</label>
                                <input type="text" id="totalSqft" class="form-control" readonly>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Amount</label>
                                <input type="text" id="totalAmount" class="form-control" readonly>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Generate Quotation</button>
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
        function filterProducts() {
            let categoryId = $('#iProductCategoryId').val();

            $('#iProductId option').show();

            if (categoryId != '') {
                $('#iProductId option').each(function () {
                    let optionCategory = $(this).data('category');

                    if ($(this).val() != '' && optionCategory != categoryId) {
                        $(this).hide();
                    }
                });

                $('#iProductId').val('');
            }
        }

        function calculateAmount() {
            let height = parseFloat($('#decHeight').val()) || 0;
            let width = parseFloat($('#decWidth').val()) || 0;
            let rate = parseFloat($('#decRatePerSqft').val()) || 0;

            let sqft = height * width;
            let amount = sqft * rate;

            $('#totalSqft').val(sqft.toFixed(2));
            $('#totalAmount').val(amount.toFixed(2));
        }

        $('#iProductCategoryId').on('change', function () {
            filterProducts();
        });

        $('#decHeight, #decWidth, #decRatePerSqft').on('keyup change', function () {
            calculateAmount();
        });

        filterProducts();
        calculateAmount();
    });
</script>
@endsection
