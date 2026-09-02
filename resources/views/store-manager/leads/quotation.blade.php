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

        .quotation-history-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .quotation-history-card .card-body {
            padding: 1.25rem;
        }

        .quotation-history-table thead th {
            font-size: 0.82rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #334155;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .quotation-history-table tbody td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .history-version-badge {
            font-weight: 600;
            font-size: 0.78rem;
            background: #eef2ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
            border-radius: 999px;
            padding: 0.35rem 0.6rem;
        }

        .history-eye-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .history-modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .history-modal-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @include('common.alert')

                @php
                    // Preserve old input on validation errors.
                    // Otherwise prefill with last generated quotation (active batch).
                    $existingItems = old('items', []);

                    if (empty($existingItems)) {
                        $existingItems = $activeQuotations
                            ->map(function ($quotation) {
                                return [
                                    'iProductCategoryId' => $quotation->iProductCategoryId,
                                    'iProductId' => $quotation->iProductId,
                                    'unit_of_measurement' => $quotation->unit_of_measurement,
                                    'shape_id' => $quotation->shape_id,
                                    'feature_id' => $quotation->feature_id,
                                    'remarks' => $quotation->remarks,
                                    'quantity' => $quotation->quantity,
                                    'calculation_multiple' => (int) ($quotation->calculation_multiple ?? 3),
                                    'decHeight' => $quotation->decHeight,
                                    'decWidth' => $quotation->decWidth,
                                    'decRatePerSqft' => $quotation->decRatePerSqft,
                                    'iAmount' => $quotation->iAmount,
                                ];
                            })
                            ->values()
                            ->toArray();
                    }

                    if (empty($existingItems)) {
                        $existingItems = [
                            [
                                'iProductCategoryId' => '',
                                'iProductId' => '',
                                'unit_of_measurement' => '',
                                'shape_id' => '',
                                'feature_id' => '',
                                'remarks' => '',
                                'quantity' => 1,
                                'calculation_multiple' => 3,
                                'decHeight' => '',
                                'decWidth' => '',
                                'decRatePerSqft' => '',
                                'iAmount' => '',
                            ],
                        ];
                    }

                    $productOptions = $products
                        ->map(function ($product) {
                            return [
                                'id' => $product->iProductId,
                                'name' => $product->strProductName,
                                'category' => $product->iCategoryId,
                            ];
                        })
                        ->values()
                        ->toArray();
                @endphp

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Lead Quotation</h4>
                            <div class="page-title-right">
                                @if ($lead->quotation)
                                    <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}"
                                        class="btn btn-primary btn-sm">
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

                @if ($quotationVersions->isNotEmpty())
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Quotation Versions:</strong>
                            @foreach ($quotationVersions as $version)
                                <span class="badge bg-light text-dark border ms-1">
                                    #{{ $version->quotation_batch_id }} · {{ $version->line_items }} items ·
                                    ₹{{ number_format((float) $version->amount, 2) }}
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
                                    <p><strong>Only Fitting Quotation:</strong>
                                        {{ !empty($lead->IsOnlyFittingQuotation) ? 'Yes' : 'No' }}</p>
                                    <p><strong>Fitting Required:</strong>
                                        {{ !empty($lead->isFittingRequired) ? 'Yes' : 'No' }}</p>
                                    <p><strong>Fitting Type:</strong>
                                        @if (!empty($lead->isFittingRequired))
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
                                    <p><strong>Measurement Required:</strong>
                                        {{ !empty($lead->IsMeasureMentRequired) ? 'Yes' : 'No' }}</p>
                                    <p><strong>Measurement Date:</strong> {{ $lead->MeasurementVisitDate ?: '-' }}</p>
                                    <p><strong>Next Follow Up:</strong> {{ $lead->NetFollowupdate ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('store.leads.save-quotation', $lead->iLeadId) }}" method="POST"
                            id="quotationForm">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addItemRow">
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </div>
                            </div>

                            <div id="quotationRows">
                                @foreach ($existingItems as $index => $item)
                                    <div class="quotation-row card-like" data-index="{{ $index }}">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                            <strong>Product #<span class="row-number">{{ $index + 1 }}</span></strong>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"
                                                {{ count($existingItems) === 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-2">
                                                <label class="form-label">Category <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][iProductCategoryId]"
                                                    class="form-control row-category-select" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->iCategoryId }}"
                                                            {{ (string) ($item['iProductCategoryId'] ?? '') === (string) $category->iCategoryId ? 'selected' : '' }}>
                                                            {{ $category->strCategoryName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Product <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][iProductId]"
                                                    class="form-control row-product-select" required>
                                                    <option value="">Select Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->iProductId }}"
                                                            data-category="{{ $product->iCategoryId }}"
                                                            {{ (string) ($item['iProductId'] ?? '') === (string) $product->iProductId ? 'selected' : '' }}>
                                                            {{ $product->strProductName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-1">
                                                <label class="form-label">Unit <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][unit_of_measurement]"
                                                    class="form-control unit-of-measurement" required>

                                                    <option value="inch"
                                                        {{ ($item['unit_of_measurement'] ?? '') === 'inch' ? 'selected' : '' }}>
                                                        Inch</option>
                                                    <option value="MM"
                                                        {{ ($item['unit_of_measurement'] ?? '') === 'MM' ? 'selected' : '' }}>
                                                        MM</option>
                                                    <option value="Feet"
                                                        {{ ($item['unit_of_measurement'] ?? '') === 'Feet' ? 'selected' : '' }}>
                                                        Feet</option>
                                                </select>
                                            </div>

                                            <div class="col-md-1">
                                                <label class="form-label">Shape <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][shape_id]" class="form-control"
                                                    required>
                                                    <option value="">Shape</option>
                                                    @foreach ($shapes as $shape)
                                                        <option value="{{ $shape->shape_id }}"
                                                            {{ (string) ($item['shape_id'] ?? '') === (string) $shape->shape_id ? 'selected' : '' }}>
                                                            {{ $shape->shape_title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-1">
                                                <label class="form-label">Feature <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][feature_id]" class="form-control"
                                                    required>
                                                    <option value="">Feature</option>
                                                    @foreach ($features as $feature)
                                                        <option value="{{ $feature->feature_id }}"
                                                            {{ (string) ($item['feature_id'] ?? '') === (string) $feature->feature_id ? 'selected' : '' }}>
                                                            {{ $feature->feature_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-1">
                                                <label class="form-label">Qty <span style="color:red;">*</span></label>
                                                <input type="number" min="1"
                                                    name="items[{{ $index }}][quantity]"
                                                    class="form-control quantity" value="{{ $item['quantity'] ?? 1 }}"
                                                    required>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Calc By <span style="color:red;">*</span></label>
                                                <select name="items[{{ $index }}][calculation_multiple]"
                                                    class="form-control calc-multiple" required>
                                                    <option value="3"
                                                        {{ (string) ($item['calculation_multiple'] ?? '3') === '3' ? 'selected' : '' }}>
                                                        3</option>
                                                    <option value="6"
                                                        {{ (string) ($item['calculation_multiple'] ?? '') === '6' ? 'selected' : '' }}>
                                                        6</option>
                                                </select>
                                            </div>


                                            <div class="col-md-1">
                                                <label class="form-label">Width <span style="color:red;">*</span></label>
                                                <input type="number" step="0.01" min="0"
                                                    name="items[{{ $index }}][decWidth]"
                                                    class="form-control decWidth" value="{{ $item['decWidth'] ?? '' }}"
                                                    required>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Height <span style="color:red;">*</span></label>
                                                <input type="number" step="0.01" min="0"
                                                    name="items[{{ $index }}][decHeight]"
                                                    class="form-control decHeight" value="{{ $item['decHeight'] ?? '' }}"
                                                    required>
                                            </div>
                                            {{-- 20-04-2026 --}}
                                            <div class="col-md-1">
                                                <label class="form-label">Sqft</label>
                                                <input type="text" class="form-control lineSqft" readonly>
                                            </div>
                                            {{-- 20-04-2026 --}}

                                            <div class="col-md-1">
                                                <label class="form-label">Rate <span style="color:red;">*</span></label>
                                                <input type="number" step="any" min="0"
                                                    name="items[{{ $index }}][decRatePerSqft]"
                                                    class="form-control decRatePerSqft"
                                                    value="{{ $item['decRatePerSqft'] ?? '' }}" required>
                                            </div>

                                            <div class="col-md-1">
                                                <label class="form-label">Amount</label>
                                                <input type="text" name="items[{{ $index }}][iAmount]"
                                                    class="form-control lineAmount" value="{{ $item['iAmount'] ?? '' }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Remarks</label>
                                                <input type="text" name="items[{{ $index }}][remarks]"
                                                    class="form-control" value="{{ $item['remarks'] ?? '' }}"
                                                    placeholder="Enter remarks">
                                            </div>
                                        </div>

                                        <div class="product-separator mt-3"></div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-2 card-body">
                                {{-- new code 17-04-2026 --}}
                                <div class="row mt-2 card-body">

                                    <!--
                                                                                                                                                                    <div class="col-md-4 mb-4 fitting-charge-box">
                                                                                                                                                                        <label class="form-label">Fitting Charges</label>
                                                                                                                                                                        <input type="number" step="0.01" min="0" name="iFittingCharges"
                                                                                                                                                                            id="iFittingCharges" class="form-control"
                                                                                                                                                                            value="{{ old('iFittingCharges', $lead->iFittingCharges) }}">
                                                                                                                                                                        @error('iFittingCharges')
        <span class="text-danger d-block">{{ $message }}</span>
    @enderror
                                                                                                                                                                    </div> -->
                                    <div class="col-md-4 mb-4 fitting-charge-box">
                                        <label class="form-label">Fitting Charges</label>
                                        <input type="number" step="0.01" min="0" name="iFittingCharges"
                                            id="iFittingCharges" class="form-control"
                                            value="{{ old('iFittingCharges', $lead->iFittingCharges) }}">
                                        @error('iFittingCharges')
                                            <span class="text-danger d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- <div class="col-md-4 mb-4">
                                                                                                                                                                        <label class="form-label">Delivery Charges</label>
                                                                                                                                                                        <input type="number" step="0.01" min="0" name="delivery_charges"
                                                                                                                                                                            id="deliveryCharges" class="form-control"
                                                                                                                                                                            value="{{ old('delivery_charges', 0) }}">
                                                                                                                                                                    </div> -->
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Delivery Charges</label>
                                        <input type="number" step="0.01" min="0" name="delivery_charges"
                                            id="deliveryCharges" class="form-control"
                                            value="{{ old('delivery_charges', (float) ($lead->delivery_charges ?? 0)) }}">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">Packing Charges</label>
                                        <input type="number" step="0.01" min="0" name="packing_charges"
                                            id="packingCharges" class="form-control"
                                            value="{{ old('packing_charges', (float) ($lead->packing_charges ?? 0)) }}">
                                        @error('packing_charges')
                                            <span class="text-danger d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    {{-- 20-04-26 --}}
                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Total Sqft</label>
                                        <input type="text" id="totalSqftAmount" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Total Product Qty</label>
                                        <input type="text" id="totalQtyAmount" class="form-control" readonly>
                                    </div>

                                    {{-- 20-04-26 --}}
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" id="subtotalAmount" class="form-control" readonly>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Next Follow Up Date <span
                                            style="color:red;">*</span></label>
                                    <input type="date" name="followup_date" class="form-control"
                                        value="{{ old('followup_date', $lead->NetFollowupdate) }}" required>
                                    @error('followup_date')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-4">
                                    <!-- <label class="form-label">Fitting Required?</label>
                                    <input type="text" class="form-control"
                                        value="{{ !empty($lead->isFittingRequired) ? 'Yes' : 'No' }}" readonly> -->
                                    <label class="form-label">Fitting Required? <span style="color:red;">*</span></label>
                                    <select name="isFittingRequired" id="isFittingRequired" class="form-control" required>
                                        <option value="0"
                                            {{ old('isFittingRequired', (int) ($lead->isFittingRequired ?? 0)) == 0 ? 'selected' : '' }}>
                                            No</option>
                                        <option value="1"
                                            {{ old('isFittingRequired', (int) ($lead->isFittingRequired ?? 0)) == 1 ? 'selected' : '' }}>
                                            Yes</option>
                                    </select>
                                    @error('isFittingRequired')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-4 fitting-type-box">
                                    <label class="form-label">Fitting Type</label>
                                    <input type="text" class="form-control"
                                        value="{{ !empty($lead->isFittingRequired) ? (!empty($lead->isFittingChargeIncluded) ? 'Included' : 'Extra') : 'N/A' }}"
                                        readonly>
                                </div>

                                {{-- <div class="col-md-4 mb-4 fitting-charge-box">
                                    <label class="form-label">Fitting Charges</label>
                                    <input type="number" step="0.01" min="0" name="iFittingCharges"
                                        id="iFittingCharges" class="form-control"
                                        value="{{ old('iFittingCharges', $lead->iFittingCharges) }}">
                                    @error('iFittingCharges')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Discount?</label>
                                    <select name="isDiscountApplicable" id="isDiscountApplicable" class="form-control"
                                        required>
                                        <option value="0"
                                            {{ old('isDiscountApplicable', (int) ($lead->isDiscountApplicable ?? 0)) == 0 ? 'selected' : '' }}>
                                            No</option>
                                        <option value="1"
                                            {{ old('isDiscountApplicable', (int) ($lead->isDiscountApplicable ?? 0)) == 1 ? 'selected' : '' }}>
                                            Yes</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-4" id="discountAmountBox">
                                    <label class="form-label">Discount Amount</label>
                                    <input type="number" step="0.01" min="0" name="discount_amount"
                                        id="discountAmount" class="form-control"
                                        value="{{ old('discount_amount', (float) ($lead->decDiscountAmount ?? 0)) }}">
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">GST (18%)?</label>
                                    <select name="isGstApplicable" id="isGstApplicable" class="form-control" required>
                                        <option value="0"
                                            {{ old('isGstApplicable', (int) ($lead->isGstApplicable ?? 0)) == 0 ? 'selected' : '' }}>
                                            No</option>
                                        <option value="1"
                                            {{ old('isGstApplicable', (int) ($lead->isGstApplicable ?? 0)) == 1 ? 'selected' : '' }}>
                                            Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Amount Before GST</label>
                                    <input type="text" id="amountBeforeGst" class="form-control" readonly>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label class="form-label">GST Amount</label>
                                    <input type="text" id="gstAmount" class="form-control" readonly>
                                </div>


                                <div class="col-md-4 mb-4">
                                    <label class="form-label">Grand Total</label>
                                    <input type="text" id="grandTotalAmount" class="form-control" readonly>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Comment</label>
                                    <textarea name="strComments" class="form-control" rows="3" placeholder="Enter quotation comment">{{ old('strComments') }}</textarea>
                                    @error('strComments')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Save Quotation</button>
                                <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $currentFittingCharges = (float) ($lead->iFittingCharges ?? 0);
                    $currentDeliveryCharges = (float) ($lead->delivery_charges ?? 0);
                    $currentPackingCharges = (float) ($lead->packing_charges ?? 0);
                    $currentDiscount =
                        (int) ($lead->isDiscountApplicable ?? 0) === 1 ? (float) ($lead->decDiscountAmount ?? 0) : 0;
                @endphp

                @if (($quotationHistoryBatches ?? collect())->isNotEmpty())
                    <div class="card mt-4 quotation-history-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="mb-0">Quotation History (All Versions)</h5>
                                <small class="text-muted">Click the eye icon to view product + amount details</small>
                            </div>
                            <p class="small text-muted mb-3">
                                Note: Fitting/Delivery/Packing/Discount/GST are shown using current lead charges for quick
                                comparison.
                            </p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover quotation-history-table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Version</th>
                                            <th>Date</th>
                                            <th>Line Items</th>
                                            <th>Subtotal</th>
                                            <th>Fitting</th>
                                            <th>Delivery</th>
                                            <th>Packing</th>
                                            <th>Discount</th>
                                            <th>GST</th>
                                            <th>Grand Total</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotationHistoryBatches as $batch)
                                            @php
                                                $batchBeforeDiscount =
                                                    (float) $batch->subtotal +
                                                    $currentFittingCharges +
                                                    $currentDeliveryCharges +
                                                    $currentPackingCharges;
                                                $batchDiscount = min($currentDiscount, $batchBeforeDiscount);
                                                $batchTaxable = max($batchBeforeDiscount - $batchDiscount, 0);
                                                $batchGst =
                                                    (int) ($lead->isGstApplicable ?? 0) === 1
                                                        ? $batchTaxable * 0.18
                                                        : 0;
                                                $batchGrandTotal = $batchTaxable + $batchGst;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="history-version-badge">
                                                        #{{ $batch->quotation_batch_id }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ optional($batch->created_at)->format('d-m-Y h:i A') ?: '-' }}
                                                </td>
                                                <td>{{ $batch->line_items }}</td>
                                                <td>₹{{ number_format((float) $batch->subtotal, 2) }}</td>
                                                <td>₹{{ number_format($currentFittingCharges, 2) }}</td>
                                                <td>₹{{ number_format($currentDeliveryCharges, 2) }}</td>
                                                <td>₹{{ number_format($currentPackingCharges, 2) }}</td>
                                                <td>- ₹{{ number_format($batchDiscount, 2) }}</td>
                                                <td>₹{{ number_format($batchGst, 2) }}</td>
                                                <td><strong>₹{{ number_format($batchGrandTotal, 2) }}</strong></td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary history-eye-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#quotationBatchModal{{ $batch->quotation_batch_id }}"
                                                        title="View product details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
    @if (($quotationHistoryBatches ?? collect())->isNotEmpty())
        @foreach ($quotationHistoryBatches as $batch)
            <div class="modal fade" id="quotationBatchModal{{ $batch->quotation_batch_id }}" tabindex="-1"
                aria-labelledby="quotationBatchModalLabel{{ $batch->quotation_batch_id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header history-modal-header">
                            <h5 class="modal-title history-modal-title"
                                id="quotationBatchModalLabel{{ $batch->quotation_batch_id }}">
                                Quotation Version #{{ $batch->quotation_batch_id }} — Product Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Product</th>
                                            <th>Shape</th>
                                            <th>Feature</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Width</th>
                                            <th>Height</th>
                                            <th>Sqft</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($batch->items as $itemIndex => $historyItem)
                                            <tr>
                                                <td>{{ $itemIndex + 1 }}</td>
                                                <td>{{ $historyItem->category->strCategoryName ?? '-' }}</td>
                                                <td>{{ $historyItem->product->strProductName ?? '-' }}</td>
                                                <td>{{ $historyItem->shape->shape_title ?? '-' }}</td>
                                                <td>{{ $historyItem->feature->feature_name ?? '-' }}</td>
                                                <td>{{ (int) ($historyItem->quantity ?? 0) }}</td>
                                                <td>{{ $historyItem->unit_of_measurement ?? '-' }}</td>
                                                <td>{{ number_format((float) ($historyItem->decWidth ?? 0), 2) }}</td>
                                                <td>{{ number_format((float) ($historyItem->decHeight ?? 0), 2) }}</td>
                                                <td>{{ number_format((float) ($historyItem->decTotalSqft ?? 0), 2) }}</td>
                                                <td>₹{{ number_format((float) ($historyItem->decRatePerSqft ?? 0), 2) }}
                                                </td>
                                                <td>₹{{ number_format((float) ($historyItem->iAmount ?? 0), 2) }}</td>
                                                <td>{{ $historyItem->remarks ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="11" class="text-end">Total Sqft</th>
                                            <th colspan="2">
                                                {{ number_format((float) $batch->items->sum('decTotalSqft'), 2) }}</th>
                                            {{-- <th colspan="10" class="text-end">Subtotal</th>
                                            <th colspan="2">₹{{ number_format((float) $batch->subtotal, 2) }}</th> --}}
                                        </tr>
                                        <tr>
                                            <th colspan="11" class="text-end">Subtotal</th>
                                            <th colspan="2">₹{{ number_format((float) $batch->subtotal, 2) }}</th>
                                        </tr>
                                        @php
                                            $batchBeforeDiscount =
                                                (float) $batch->subtotal +
                                                $currentFittingCharges +
                                                $currentDeliveryCharges +
                                                $currentPackingCharges;
                                            $batchDiscount = min($currentDiscount, $batchBeforeDiscount);
                                            $batchTaxable = max($batchBeforeDiscount - $batchDiscount, 0);
                                            $batchGst =
                                                (int) ($lead->isGstApplicable ?? 0) === 1 ? $batchTaxable * 0.18 : 0;
                                            $batchGrandTotal = $batchTaxable + $batchGst;
                                        @endphp
                                        <tr>
                                            <th colspan="10" class="text-end">Fitting Charges</th>
                                            <th colspan="2">₹{{ number_format($currentFittingCharges, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-end">Delivery Charges</th>
                                            <th colspan="2">₹{{ number_format($currentDeliveryCharges, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-end">Packing Charges</th>
                                            <th colspan="2">₹{{ number_format($currentPackingCharges, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-end">Discount</th>
                                            <th colspan="2">- ₹{{ number_format($batchDiscount, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-end">Taxable Amount</th>
                                            <th colspan="2">₹{{ number_format($batchTaxable, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10" class="text-end">GST (18%)</th>
                                            <th colspan="2">₹{{ number_format($batchGst, 2) }}</th>
                                        </tr>
                                        <tr class="table-success">
                                            <th colspan="10" class="text-end">Grand Total</th>
                                            <th colspan="2">₹{{ number_format($batchGrandTotal, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const productOptions = {!! json_encode($productOptions) !!};
            /*const leadFittingRequired = "{{ (int) ($lead->isFittingRequired ?? 0) }}";
            const leadFittingChargeIncluded = "{{ (int) ($lead->isFittingChargeIncluded ?? 0) }}";*/

            function buildProductSelectOptions(categoryId, selectedProductId) {
                let html = '<option value="">Select Product</option>';

                productOptions.forEach(function(product) {
                    if (categoryId === '' || String(product.category) === String(categoryId)) {
                        const selected = String(selectedProductId || '') === String(product.id) ?
                            ' selected' : '';
                        html += '<option value="' + product.id + '" data-category="' + product.category +
                            '"' + selected + '>' + product.name + '</option>';
                    }
                });

                return html;
            }

            function refreshRowNumbersAndInputNames() {
                $('#quotationRows .quotation-row').each(function(index) {
                    $(this).attr('data-index', index);
                    $(this).find('select.row-category-select').attr('name', 'items[' + index +
                        '][iProductCategoryId]');
                    $(this).find('select.row-product-select').attr('name', 'items[' + index +
                        '][iProductId]');
                    $(this).find('select.unit-of-measurement').attr('name', 'items[' + index +
                        '][unit_of_measurement]');
                    $(this).find('select[name*="[shape_id]"]').attr('name', 'items[' + index +
                        '][shape_id]');
                    $(this).find('select[name*="[feature_id]"]').attr('name', 'items[' + index +
                        '][feature_id]');
                    $(this).find('input[name*="[remarks]"]').attr('name', 'items[' + index + '][remarks]');
                    $(this).find('input.quantity').attr('name', 'items[' + index + '][quantity]');
                    $(this).find('input.decHeight').attr('name', 'items[' + index + '][decHeight]');
                    $(this).find('input.decWidth').attr('name', 'items[' + index + '][decWidth]');
                    $(this).find('input.decRatePerSqft').attr('name', 'items[' + index +
                        '][decRatePerSqft]');
                    $(this).find('input.lineAmount').attr('name', 'items[' + index + '][iAmount]');
                    $(this).find('select.calc-multiple').attr('name', 'items[' + index +
                        '][calculation_multiple]');
                });

                $('.remove-row').prop('disabled', $('#quotationRows .quotation-row').length === 1);
            }

            function updateProductDropdownForRow($row) {
                const categoryId = $row.find('.row-category-select').val();
                const $productSelect = $row.find('.row-product-select');
                const currentVal = $productSelect.val();


                $productSelect.html(buildProductSelectOptions(categoryId, currentVal));

                if (currentVal && $productSelect.find('option[value="' + currentVal + '"]').length === 0) {
                    $productSelect.val('');
                }
            }

            function updateAllProductDropdowns() {
                $('#quotationRows .quotation-row').each(function() {
                    updateProductDropdownForRow($(this));
                });
            }

            function toggleFittingCharges() {
                // ❌ remove all conditions
                $('.fitting-type-box').show();
                $('.fitting-charge-box').show();
            }
            // function toggleFittingCharges() {
            //     if (leadFittingRequired === '1' && leadFittingChargeIncluded === '0') {
            //         $('.fitting-type-box').show();
            //         $('.fitting-charge-box').show();
            //     } else if (leadFittingRequired === '1' && leadFittingChargeIncluded === '1') {
            //         $('.fitting-type-box').show();
            //         $('.fitting-charge-box').hide();
            //         $('#iFittingCharges').val(0);
            //     } else {
            //         $('.fitting-type-box').hide();
            //         $('.fitting-charge-box').hide();
            //         $('#iFittingCharges').val(0);
            //     }
            // }

            function toggleDiscountBox() {
                const isDiscountApplicable = $('#isDiscountApplicable').val() === '1';
                if (isDiscountApplicable) {
                    $('#discountAmountBox').show();
                } else {
                    $('#discountAmountBox').hide();
                    $('#discountAmount').val(0);
                }
            }


            function recalculateTotals() {
                let subtotal = 0;
                let totalSqft = 0;
                let totalQty = 0;


                $('#quotationRows .quotation-row').each(function() {
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    const height = parseFloat($(this).find('.decHeight').val()) || 0;
                    const width = parseFloat($(this).find('.decWidth').val()) || 0;
                    const rate = parseFloat($(this).find('.decRatePerSqft').val()) || 0;
                    const multiple = parseInt($(this).find('.calc-multiple').val(), 10) || 3;

                    //code 
                    const unit = $(this).find('.unit-of-measurement').val();
                    let finalHeight = 0;
                    let finalWidth = 0;
                    if (unit === 'Feet') {
                        finalHeight = height;
                        finalWidth = width;
                    } else {
                        const hData = calculateAdjustedValues(height, unit, multiple);
                        const wData = calculateAdjustedValues(width, unit, multiple);

                        finalHeight = hData.feet;
                        finalWidth = wData.feet;
                    }
                    const lineSqft = qty * finalHeight * finalWidth;
                    const lineAmount = Math.round(qty * finalHeight * finalWidth * rate);

                    // const calculationHeight = normalizeDimensionForAmount(height, multiple);
                    // const calculationWidth = normalizeDimensionForAmount(width, multiple);
                    // const lineAmount = qty * calculationHeight * calculationWidth * rate;

                    subtotal += lineAmount;
                    totalSqft += lineSqft;
                    totalQty += qty;
                    $(this).find('.lineSqft').val(lineSqft.toFixed(2));
                    $(this).find('.lineAmount').val(lineAmount.toFixed(0));

                });

                // 17-04-2026
                const fitting = parseFloat($('#iFittingCharges').val()) || 0;
                const delivery = parseFloat($('#deliveryCharges').val()) || 0;
                const packing = parseFloat($('#packingCharges').val()) || 0;
                const baseAmount = subtotal + fitting + delivery + packing;
                // 17-04-2026

                const isDiscountApplicable = $('#isDiscountApplicable').val() === '1';
                const discount = isDiscountApplicable ? (parseFloat($('#discountAmount').val()) || 0) : 0;
                const discountAmount = Math.min(discount, baseAmount);
                const afterDiscount = baseAmount - discountAmount;

                const isGstApplicable = $('#isGstApplicable').val() === '1';
                const gst = isGstApplicable ? (afterDiscount * 0.18) : 0;


                //$('#subtotalAmount').val(baseAmount.toFixed(2));
                $('#subtotalAmount').val(Math.round(baseAmount));
                $('#totalSqftAmount').val(totalSqft.toFixed(2));
                $('#totalQtyAmount').val(totalQty.toFixed(0));
                $('#amountBeforeGst').val(Math.round(afterDiscount));
                $('#gstAmount').val(Math.round(gst));
                $('#grandTotalAmount').val(Math.round(afterDiscount + gst));

                /*$('#amountBeforeGst').val(afterDiscount.toFixed(2));
                $('#gstAmount').val(gst.toFixed(2));
                $('#grandTotalAmount').val((afterDiscount + gst).toFixed(2));*/

            }

            function calculateAdjustedValues(value, unit, multiple) {

                if (!value || value <= 0) {
                    return {
                        inch: 0,
                        adjustedInch: 0,
                        feet: 0
                    };
                }

                let inchValue = value;

                // 🔹 MM → Inch
                if (unit === 'MM') {
                    inchValue = value / 25.4;
                }

                // 🔹 Feet direct
                if (unit === 'Feet') {
                    return {
                        inch: value * 12,
                        adjustedInch: value * 12,
                        feet: value
                    };
                }

                // 🔹 Excel logic (IMPORTANT)
                let adjustedInch = Math.ceil(inchValue / multiple) * multiple;

                let feet = adjustedInch / 12;

                return {
                    inch: inchValue,
                    adjustedInch: adjustedInch,
                    feet: feet
                };
            }
            // function normalizeDimensionForAmount(value, multiple) {
            //     if (!value || value <= 0) {
            //         return 0;
            //     }

            //     const selectedMultiple = multiple === 6 ? 6 : 3;
            //     return Math.ceil(value / selectedMultiple) * selectedMultiple;
            // }

            function addItemRow() {
                const nextIndex = $('#quotationRows .quotation-row').length;
                const categoryId = '';

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
                <label class="form-label">Category <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][iProductCategoryId]" class="form-control row-category-select" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->iCategoryId }}">{{ $category->strCategoryName }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-md-2">
                <label class="form-label">Product <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][iProductId]" class="form-control row-product-select" required>
                    ${buildProductSelectOptions(categoryId, '')}
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Unit <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][unit_of_measurement]" class="form-control unit-of-measurement" required>
                    
                    <option value="inch">Inch</option>
                    <option value="MM">MM</option>
                    <option value="Feet">Feet</option>
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Shape <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][shape_id]" class="form-control" required>
                    <option value="">Shape</option>
                    @foreach ($shapes as $shape)
                        <option value="{{ $shape->shape_id }}">{{ $shape->shape_title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Feature <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][feature_id]" class="form-control" required>
                    <option value="">Feature</option>
                    @foreach ($features as $feature)
                        <option value="{{ $feature->feature_id }}">{{ $feature->feature_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">Qty <span style="color:red;">*</span></label>
                <input type="number" min="1" name="items[${nextIndex}][quantity]" class="form-control quantity" value="1" required>
            </div>
<div class="col-md-1">
                <label class="form-label">Calc By <span style="color:red;">*</span></label>
                <select name="items[${nextIndex}][calculation_multiple]" class="form-control calc-multiple" required>
                    <option value="3" selected>3</option>
                    <option value="6">6</option>
                </select>
            </div>
          

            <div class="col-md-1">
                <label class="form-label">Width <span style="color:red;">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${nextIndex}][decWidth]" class="form-control decWidth" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">Height <span style="color:red;">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${nextIndex}][decHeight]" class="form-control decHeight" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">Sqft</label>
                <input type="text" class="form-control lineSqft" readonly>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">Rate <span style="color:red;">*</span></label>
                <input type="number" step="any" min="0" name="items[${nextIndex}][decRatePerSqft]" class="form-control decRatePerSqft" required>
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

            $('#addItemRow').on('click', function() {
                addItemRow();
            });

            $(document).on('click', '.remove-row', function() {
                if ($('#quotationRows .quotation-row').length > 1) {
                    $(this).closest('.quotation-row').remove();
                    refreshRowNumbersAndInputNames();
                    recalculateTotals();
                }
            });

            $(document).on('change', '.row-category-select', function() {
                const $row = $(this).closest('.quotation-row');
                updateProductDropdownForRow($row);
                recalculateTotals();
            });

            $(document).on('input change',
                '.quantity, .decHeight, .decWidth, .calc-multiple, .decRatePerSqft, .unit-of-measurement, #iFittingCharges, #deliveryCharges, #packingCharges, .row-product-select, #isDiscountApplicable, #discountAmount, #isGstApplicable',
                function() {
                    toggleDiscountBox();
                    recalculateTotals();
                });

            updateAllProductDropdowns();
            refreshRowNumbersAndInputNames();
            toggleFittingCharges();
            toggleDiscountBox();
            recalculateTotals();
        });
    </script>
@endsection
