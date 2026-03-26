@extends('layouts.app')

@section('title', 'Lead Quotation')

@section('styles')
<style>
    .quotation-row {
        background: #fff;
    }

    .quotation-row .card-like {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fafafa;
    }

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
</style>
@endsection

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

            @php
                $existingItems = old('items', $lead->quotations->map(function ($q) {
                    return array(
                        'iProductCategoryId' => $q->iProductCategoryId,
                        'iProductId' => $q->iProductId,
                        'decHeight' => $q->decHeight,
                        'decWidth' => $q->decWidth,
                        'decRatePerSqft' => $q->decRatePerSqft,
                    );
                })->toArray());

                if (empty($existingItems)) {
                    $existingItems = array(
                        array(
                            'iProductCategoryId' => '',
                            'iProductId' => '',
                            'decHeight' => '',
                            'decWidth' => '',
                            'decRatePerSqft' => '',
                        )
                    );
                }

                $selectedProductIds = collect(old('iProductIds', collect($existingItems)->pluck('iProductId')->filter()->values()->toArray()))
                    ->map(function ($id) {
                        return (string) $id;
                    })
                    ->toArray();

                $productOptions = $products->map(function ($product) {
                    return array(
                        'id' => $product->iProductId,
                        'name' => $product->strProductName,
                        'category' => $product->iCategoryId,
                    );
                })->values()->toArray();
            @endphp

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
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Products <span style="color:red;">*</span></label>
                                <select name="iProductIds[]" id="iProductIds" class="form-control" multiple size="6" required>
                                    @foreach($products as $product)
                                        <option
                                            value="{{ $product->iProductId }}"
                                            data-category="{{ $product->iCategoryId }}"
                                            {{ in_array((string) $product->iProductId, $selectedProductIds, true) ? 'selected' : '' }}
                                        >
                                            {{ $product->strProductName }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Press Ctrl or Cmd to select multiple products.</small>
                                @error('iProductIds')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                                @error('iProductIds.*')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Quotation Items</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemRow">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>

                        <div id="quotationRows">
                            @foreach($existingItems as $index => $item)
                                <div class="quotation-row card-like" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <strong>Product #<span class="row-number">{{ $index + 1 }}</span></strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" {{ count($existingItems) === 1 ? 'disabled' : '' }}>
                                            Remove
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Product <span style="color:red;">*</span></label>
                                            <select name="items[{{ $index }}][iProductId]" class="form-control product-select row-product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option
                                                        value="{{ $product->iProductId }}"
                                                        data-category="{{ $product->iCategoryId }}"
                                                        {{ (string)($item['iProductId'] ?? '') === (string)$product->iProductId ? 'selected' : '' }}
                                                    >
                                                        {{ $product->strProductName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('items.' . $index . '.iProductId')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Height <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decHeight]" class="form-control decHeight" value="{{ $item['decHeight'] ?? '' }}" required>
                                            @error('items.' . $index . '.decHeight')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Width <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decWidth]" class="form-control decWidth" value="{{ $item['decWidth'] ?? '' }}" required>
                                            @error('items.' . $index . '.decWidth')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Rate Per Sqft <span style="color:red;">*</span></label>
                                            <input type="number" step="0.01" min="0" name="items[{{ $index }}][decRatePerSqft]" class="form-control decRatePerSqft" value="{{ $item['decRatePerSqft'] ?? '' }}" required>
                                            @error('items.' . $index . '.decRatePerSqft')
                                                <span class="text-danger d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Amount</label>
                                            <input type="text" class="form-control lineAmount" readonly>
                                        </div>

                                        <div class="col-md-1 mb-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-row w-100" {{ count($existingItems) === 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($errors->has('items') || $errors->has('items.*'))
                            <div class="text-danger mb-3">Please review quotation line items.</div>
                        @endif

                        <div class="row mt-2">
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

        function buildProductSelectOptions(selectedProductId) {
            let html = '<option value="">Select Product</option>';

            productOptions.forEach(function (product) {
                const selected = String(selectedProductId || '') === String(product.id) ? ' selected' : '';
                html += '<option value="' + product.id + '" data-category="' + product.category + '"' + selected + '>' + product.name + '</option>';
            });

            return html;
        }

        function refreshRowNumbersAndInputNames() {
            $('#quotationRows .quotation-row').each(function (index) {
                $(this).attr('data-index', index);
                $(this).find('.row-number').text(index + 1);
                $(this).find('select.row-product-select').attr('name', 'items[' + index + '][iProductId]');
                $(this).find('input.decHeight').attr('name', 'items[' + index + '][decHeight]');
                $(this).find('input.decWidth').attr('name', 'items[' + index + '][decWidth]');
                $(this).find('input.decRatePerSqft').attr('name', 'items[' + index + '][decRatePerSqft]');
            });

            $('.remove-row').prop('disabled', $('#quotationRows .quotation-row').length === 1);
        }

        function filterProductsByCategory() {
            const categoryId = $('#iProductCategoryId').val();

            $('#iProductIds option').each(function () {
                const optionCategory = String($(this).data('category') || '');

                if ($(this).val() === '' || categoryId === '' || optionCategory === String(categoryId)) {
                    $(this).prop('disabled', false);
                } else {
                    $(this).prop('disabled', true).prop('selected', false);
                }
            });
        }

        function syncRowProductsWithMultiSelect() {
            const selectedProducts = $('#iProductIds').val() || [];
            const categoryId = $('#iProductCategoryId').val();

            $('#quotationRows .row-product-select').each(function () {
                const $select = $(this);
                const currentVal = $select.val();

                $select.find('option').each(function () {
                    const value = $(this).val();
                    const optionCategory = String($(this).data('category') || '');

                    if (value === '') {
                        $(this).show();
                        return;
                    }

                    const allowedByCategory = categoryId === '' || optionCategory === String(categoryId);
                    const allowedBySelection = selectedProducts.indexOf(String(value)) !== -1 || String(value) === String(currentVal);

                    if (allowedByCategory && allowedBySelection) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                if (currentVal && selectedProducts.indexOf(String(currentVal)) === -1) {
                    $select.val('');
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
                const height = parseFloat($(this).find('.decHeight').val()) || 0;
                const width = parseFloat($(this).find('.decWidth').val()) || 0;
                const rate = parseFloat($(this).find('.decRatePerSqft').val()) || 0;
                const lineAmount = height * width * rate;

                subtotal += lineAmount;
                $(this).find('.lineAmount').val(lineAmount.toFixed(2));
            });

            const fitting = (leadFittingRequired === '1' && leadFittingChargeIncluded === '0')
                ? (parseFloat($('#iFittingCharges').val()) || 0)
                : 0;

            const grandTotal = subtotal + fitting;

            $('#subtotalAmount').val(subtotal.toFixed(2));
            $('#grandTotalAmount').val(grandTotal.toFixed(2));
        }

        function addItemRow() {
            const nextIndex = $('#quotationRows .quotation-row').length;

            const rowHtml = `
                <div class="quotation-row card-like" data-index="${nextIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Product #<span class="row-number">${nextIndex + 1}</span></strong>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Product <span style="color:red;">*</span></label>
                            <select name="items[${nextIndex}][iProductId]" class="form-control row-product-select" required>
                                ${buildProductSelectOptions('')}
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Height <span style="color:red;">*</span></label>
                            <input type="number" step="0.01" min="0" name="items[${nextIndex}][decHeight]" class="form-control decHeight" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Width <span style="color:red;">*</span></label>
                            <input type="number" step="0.01" min="0" name="items[${nextIndex}][decWidth]" class="form-control decWidth" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Rate Per Sqft <span style="color:red;">*</span></label>
                            <input type="number" step="0.01" min="0" name="items[${nextIndex}][decRatePerSqft]" class="form-control decRatePerSqft" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control lineAmount" readonly>
                        </div>

                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-row w-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            $('#quotationRows').append(rowHtml);
            refreshRowNumbersAndInputNames();
            syncRowProductsWithMultiSelect();
            recalculateTotals();
        }

        $('#iProductCategoryId').on('change', function () {
            filterProductsByCategory();
            syncRowProductsWithMultiSelect();
        });

        $('#iProductIds').on('change', function () {
            syncRowProductsWithMultiSelect();
        });

        $('#addItemRow').on('click', function () {
            addItemRow();
        });

        $(document).on('click', '.remove-row', function () {
            if ($('#quotationRows .quotation-row').length > 1) {
                $(this).closest('.quotation-row').remove();
                refreshRowNumbersAndInputNames();
                recalculateTotals();
            }
        });

        $(document).on('keyup change', '.decHeight, .decWidth, .decRatePerSqft, #iFittingCharges, .row-product-select', function () {
            recalculateTotals();
        });

        filterProductsByCategory();
        syncRowProductsWithMultiSelect();
        refreshRowNumbersAndInputNames();
        toggleFittingCharges();
        recalculateTotals();
    });
</script>
@endsection