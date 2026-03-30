@extends('layouts.app')

@section('title', 'Lead Quotation')

@section('styles')
<style>
    .lineAmount,
    #subtotalAmount,
    #grandTotalAmount {
        background: #f8f9fa;
    }

    .lead-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        height: 100%;
    }

    .lead-info-box h6 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .lead-info-box p {
        margin-bottom: 6px;
    }
    .quotation-row {
            background: #fff;
        }

        .quotation-row .card-like {
            border: 1px solid #dbeafe;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 18px;
            background: #fafafa;
        }

        .product-separator {
            border-top: 2px dashed #cbd5e1;
            margin-top: 12px;
            padding-top: 4px;
        }

</style>
@endsection

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            @php
                $existingItems = old('items', $activeQuotations->map(function ($q) {
                         return array(
                        'iProductId' => $q->iProductId,
                        'unit_of_measurement' => $q->unit_of_measurement ?? '',
                        'shape_id' => $q->shape_id ?? '',
                        'feature_id' => $q->feature_id ?? '',
                        'remarks' => $q->remarks ?? '',
                        'quantity' => $q->quantity ?? 1,
                        'decHeight' => $q->decHeight,
                        'decWidth' => $q->decWidth,
                        'decRatePerSqft' => $q->decRatePerSqft,
                        'iAmount' => $q->iAmount ?? '',
                    );
                })->toArray());

                if (empty($existingItems)) {
                    $existingItems = array(
                        array(
                            'iProductId' => '',
                            'unit_of_measurement' => '',
                            'shape_id' => '',
                            'feature_id' => '',
                            'remarks' => '',
                            'quantity' => 1,
                            'decHeight' => '',
                            'decWidth' => '',
                            'decRatePerSqft' => '',
                            'iAmount' => '',
                        )
                    );
                }

                $productOptions = $products->map(function ($product) {
                    return array(
                        'id' => $product->iProductId,
                        'name' => $product->strProductName,
                        'category' => $product->iCategoryId,
                    );
                })->values()->toArray();
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Lead Quotation</h4>
                        <div class="page-title-right">
                            @if($lead->quotation)
                                <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-file"></i> View Quotation Detail
                                </a>
                            @endif
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

             @if($quotationVersions->isNotEmpty())
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Quotation Versions:</strong>
                        @foreach($quotationVersions as $version)
                            <span class="badge bg-light text-dark border ms-1">
                                #{{ $version->quotation_batch_id }} · {{ $version->line_items }} items · ₹{{ number_format((float) $version->amount, 2) }}
                            </span>
                        @endforeach
                    </div>
                    <div class="small text-muted">
                        Latest version is considered active.
                    </div>
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Lead Details</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="lead-info-box">
                                <h6>Basic Info</h6>
                                <p><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                                <p><strong>Customer:</strong> {{ $lead->customer->strCustomer ?? '-' }}</p>
                                <p><strong>Mobile:</strong> {{ $lead->customer->strMobile ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="lead-info-box">
                                <h6>Fitting Info</h6>
                                <p><strong>Only Fitting Quotation:</strong> {{ !empty($lead->IsOnlyFittingQuotation) ? 'Yes' : 'No' }}</p>
                                <p><strong>Fitting Required:</strong> {{ !empty($lead->isFittingRequired) ? 'Yes' : 'No' }}</p>
                                <p><strong>Fitting Type:</strong>
                                    @if(!empty($lead->isFittingRequired))
                                        {{ !empty($lead->isFittingChargeIncluded) ? 'Included' : 'Extra' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="lead-info-box">
                                <h6>Follow Up Info</h6>
                                <p><strong>Measurement Required:</strong> {{ !empty($lead->IsMeasureMentRequired) ? 'Yes' : 'No' }}</p>
                                <p><strong>Measurement Date:</strong> {{ $lead->MeasurementVisitDate ?: '-' }}</p>
                                <p><strong>Next Follow Up:</strong> {{ $lead->NetFollowupdate ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.leads.save-quotation', $lead->iLeadId) }}" method="POST" id="quotationForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Product Category <span style="color:red;">*</span></label>
                                <select name="iProductCategoryId" id="iProductCategoryId" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->iCategoryId }}" {{ old('iProductCategoryId') == $category->iCategoryId ? 'selected' : '' }}>
                                            {{ $category->strCategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('iProductCategoryId')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addItemRow">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div>

                        <div id="quotationRows">
                            @foreach($existingItems as $index => $item)
                                <div class="quotation-row card-like" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <strong>Product #<span class="row-number">{{ $index + 1 }}</span></strong>
                                        <button type="button" class="btn btn-danger btn-sm remove-row" {{ count($existingItems) === 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label">Product <span style="color:red;">*</span></label>
                                            <select name="items[{{ $index }}][iProductId]" class="form-control row-product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option
                                                        value="{{ $product->iProductId }}"
                                                        data-category="{{ $product->iCategoryId }}"
                                                        {{ (string)($item['iProductId'] ?? '') === (string)$product->iProductId ? 'selected' : '' }}>
                                                        {{ $product->strProductName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Unit <span style="color:red;">*</span></label>
                                            <select name="items[{{ $index }}][unit_of_measurement]" class="form-control unit-of-measurement" required>
                                                <option value="">Unit</option>
                                                <option value="inch" {{ ($item['unit_of_measurement'] ?? '') === 'inch' ? 'selected' : '' }}>Inch</option>
                                                <option value="MM" {{ ($item['unit_of_measurement'] ?? '') === 'MM' ? 'selected' : '' }}>MM</option>
                                                <option value="Feet" {{ ($item['unit_of_measurement'] ?? '') === 'Feet' ? 'selected' : '' }}>Feet</option>
                                            </select>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Shape <span style="color:red;">*</span></label>
                                            <select name="items[{{ $index }}][shape_id]" class="form-control" required>
                                                <option value="">Shape</option>
                                                @foreach($shapes as $shape)
                                                    <option value="{{ $shape->shape_id }}" {{ (string)($item['shape_id'] ?? '') === (string)$shape->shape_id ? 'selected' : '' }}>
                                                        {{ $shape->shape_title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Feature <span style="color:red;">*</span></label>
                                            <select name="items[{{ $index }}][feature_id]" class="form-control" required>
                                                <option value="">Feature</option>
                                                @foreach($features as $feature)
                                                    <option value="{{ $feature->feature_id }}" {{ (string)($item['feature_id'] ?? '') === (string)$feature->feature_id ? 'selected' : '' }}>
                                                        {{ $feature->feature_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Qty <span style="color:red;">*</span></label>
                                            <input type="number" min="1" name="items[{{ $index }}][quantity]" class="form-control quantity" value="{{ $item['quantity'] ?? 1 }}" required>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Height <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decHeight]" class="form-control decHeight" value="{{ $item['decHeight'] ?? '' }}" required>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Width <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decWidth]" class="form-control decWidth" value="{{ $item['decWidth'] ?? '' }}" required>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Rate <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decRatePerSqft]" class="form-control decRatePerSqft" value="{{ $item['decRatePerSqft'] ?? '' }}" required>
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Amount</label>
                                            <input type="text" name="items[{{ $index }}][iAmount]" class="form-control lineAmount" value="{{ $item['iAmount'] ?? '' }}" readonly>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Remarks</label>
                                            <input type="text" name="items[{{ $index }}][remarks]" class="form-control" value="{{ $item['remarks'] ?? '' }}" placeholder="Enter remarks">
                                        </div>
                                    </div>

                                    <div class="product-separator mt-3"></div>
                                </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-2 card-body">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Subtotal</label>
                                <input type="text" id="subtotalAmount" class="form-control" readonly>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Next Follow Up Date <span style="color:red;">*</span></label>
                                <input type="date" name="followup_date" class="form-control" value="{{ old('followup_date', $lead->NetFollowupdate) }}" required>
                                @error('followup_date')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Fitting Required?</label>
                                <input type="text" class="form-control" value="{{ !empty($lead->isFittingRequired) ? 'Yes' : 'No' }}" readonly>
                            </div>

                            <div class="col-md-4 mb-4 fitting-type-box">
                                <label class="form-label">Fitting Type</label>
                                <input type="text" class="form-control" value="{{ !empty($lead->isFittingRequired) ? (!empty($lead->isFittingChargeIncluded) ? 'Included' : 'Extra') : 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-4 mb-4 fitting-charge-box">
                                <label class="form-label">Fitting Charges</label>
                                <input type="number" step="0.01" min="0" name="iFittingCharges" id="iFittingCharges" class="form-control" value="{{ old('iFittingCharges', $lead->iFittingCharges) }}">
                                @error('iFittingCharges')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Grand Total</label>
                                <input type="text" id="grandTotalAmount" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save Quotation</button>
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Cancel</a>
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
        const productOptions = {!! json_encode($productOptions) !!};
        const leadFittingRequired = "{{ (int) ($lead->isFittingRequired ?? 0) }}";
        const leadFittingChargeIncluded = "{{ (int) ($lead->isFittingChargeIncluded ?? 0) }}";

        function buildProductSelectOptions(categoryId, selectedProductId) {
            let html = '<option value="">Select Product</option>';

            productOptions.forEach(function (product) {
                if (categoryId === '' || String(product.category) === String(categoryId)) {
                    const selected = String(selectedProductId || '') === String(product.id) ? ' selected' : '';
                    html += '<option value="' + product.id + '" data-category="' + product.category + '"' + selected + '>' + product.name + '</option>';
                }
            });

            return html;
        }

        function refreshRowNumbersAndInputNames() {
            $('#quotationRows .quotation-row').each(function (index) {
                $(this).attr('data-index', index);
                $(this).find('.row-number').text(index + 1);
                $(this).find('select.row-product-select').attr('name', 'items[' + index + '][iProductId]');
                $(this).find('select.unit-of-measurement').attr('name', 'items[' + index + '][unit_of_measurement]');
                $(this).find('select[name*="[shape_id]"]').attr('name', 'items[' + index + '][shape_id]');
                $(this).find('select[name*="[feature_id]"]').attr('name', 'items[' + index + '][feature_id]');
                $(this).find('input[name*="[remarks]"]').attr('name', 'items[' + index + '][remarks]');
                $(this).find('input.quantity').attr('name', 'items[' + index + '][quantity]');
                $(this).find('input.decHeight').attr('name', 'items[' + index + '][decHeight]');
                $(this).find('input.decWidth').attr('name', 'items[' + index + '][decWidth]');
                $(this).find('input.decRatePerSqft').attr('name', 'items[' + index + '][decRatePerSqft]');
                $(this).find('input.lineAmount').attr('name', 'items[' + index + '][iAmount]');
            });

            $('.remove-row').prop('disabled', $('#quotationRows .quotation-row').length === 1);
        }

        function updateAllProductDropdowns() {
            const categoryId = $('#iProductCategoryId').val();

            $('#quotationRows .row-product-select').each(function () {
                const currentVal = $(this).val();
                $(this).html(buildProductSelectOptions(categoryId, currentVal));

                if (currentVal && $(this).find('option[value="' + currentVal + '"]').length === 0) {
                    $(this).val('');
                }
            });
        }

        function toggleFittingCharges() {
            if (leadFittingRequired === '1' && leadFittingChargeIncluded === '0') {
                $('.fitting-type-box').show();
                $('.fitting-charge-box').show();
            } else if (leadFittingRequired === '1' && leadFittingChargeIncluded === '1') {
                $('.fitting-type-box').show();
                $('.fitting-charge-box').hide();
                $('#iFittingCharges').val(0);
            } else {
                $('.fitting-type-box').hide();
                $('.fitting-charge-box').hide();
                $('#iFittingCharges').val(0);
            }
        }

        function recalculateTotals() {
            let subtotal = 0;

            $('#quotationRows .quotation-row').each(function () {
                const qty = parseFloat($(this).find('.quantity').val()) || 0;
                const height = parseFloat($(this).find('.decHeight').val()) || 0;
                const width = parseFloat($(this).find('.decWidth').val()) || 0;
                const rate = parseFloat($(this).find('.decRatePerSqft').val()) || 0;

                const lineAmount = qty * height * width * rate;

                subtotal += lineAmount;
                $(this).find('.lineAmount').val(lineAmount.toFixed(2));
            });

            const fitting = (leadFittingRequired === '1' && leadFittingChargeIncluded === '0')
                ? (parseFloat($('#iFittingCharges').val()) || 0)
                : 0;

            $('#subtotalAmount').val(subtotal.toFixed(2));
            $('#grandTotalAmount').val((subtotal + fitting).toFixed(2));
        }

        function addItemRow() {
            const nextIndex = $('#quotationRows .quotation-row').length;
            const categoryId = $('#iProductCategoryId').val();

            const rowHtml = `
    <div class="quotation-row card-like" data-index="${nextIndex}">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <strong>Product #<span class="row-number">${nextIndex + 1}</span></strong>
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Product <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][iProductId]" class="form-control row-product-select" required>
                    ${buildProductSelectOptions(categoryId, '')}
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Unit <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][unit_of_measurement]" class="form-control unit-of-measurement" required>
                    <option value="">Unit</option>
                    <option value="inch">Inch</option>
                    <option value="MM">MM</option>
                    <option value="Feet">Feet</option>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Shape <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][shape_id]" class="form-control" required>
                    <option value="">Shape</option>
                    @foreach($shapes as $shape)
                        <option value="{{ $shape->shape_id }}">{{ $shape->shape_title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Feature <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][feature_id]" class="form-control" required>
                    <option value="">Feature</option>
                    @foreach($features as $feature)
                        <option value="{{ $feature->feature_id }}">{{ $feature->feature_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Qty <span style="color:red;">*</span></label>
                <input type="number" min="1" name="items[${nextIndex}][quantity]" class="form-control quantity" value="1" required>
            </div>

            <div class="col-md-1">
                <label class="form-label">Height <span style="color:red;">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${nextIndex}][decHeight]" class="form-control decHeight" required>
            </div>

            <div class="col-md-1">
                <label class="form-label">Width <span style="color:red;">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${nextIndex}][decWidth]" class="form-control decWidth" required>
            </div>

            <div class="col-md-1">
                <label class="form-label">Rate <span style="color:red;">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${nextIndex}][decRatePerSqft]" class="form-control decRatePerSqft" required>
            </div>

            <div class="col-md-1">
                <label class="form-label">Amount</label>
                <input type="text" name="items[${nextIndex}][iAmount]" class="form-control lineAmount" readonly>
            </div>

            <div class="col-md-2">
                <label class="form-label">Remarks</label>
                <input type="text" name="items[${nextIndex}][remarks]" class="form-control" placeholder="Enter remarks">
            </div>
        </div>

        <div class="product-separator mt-3"></div>
    </div>

            `;

            $('#quotationRows').append(rowHtml);
            refreshRowNumbersAndInputNames();
            recalculateTotals();
        }

        $('#iProductCategoryId').on('change', function () {
            updateAllProductDropdowns();
        });

        $('#addItemRow').on('click', function () {
            if ($('#iProductCategoryId').val() === '') {
                alert('Please select Product Category first.');
                return;
            }
            addItemRow();
        });

        $(document).on('click', '.remove-row', function () {
            if ($('#quotationRows .quotation-row').length > 1) {
                $(this).closest('.quotation-row').remove();
                refreshRowNumbersAndInputNames();
                recalculateTotals();
            }
        });

        $(document).on('keyup change', '.quantity, .decHeight, .decWidth, .decRatePerSqft, #iFittingCharges, .row-product-select', function () {
            recalculateTotals();
        });

        updateAllProductDropdowns();
        refreshRowNumbersAndInputNames();
        toggleFittingCharges();
        recalculateTotals();
    });
</script>
@endsection