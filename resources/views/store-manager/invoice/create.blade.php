@extends('layouts.app')
@section('title', 'Create Invoice')

@section('styles')
<style>
    .page-card{border:0;border-radius:14px;box-shadow:0 4px 20px rgba(15,23,42,.07);}

    /* ── Row card ── */
    .item-row{
        background:#fff;
        border:1px solid #dbeafe;
        border-radius:10px;
        padding:16px 18px 10px;
        margin-bottom:14px;
        position:relative;
        transition:box-shadow .15s;
    }
    .item-row:hover{box-shadow:0 4px 14px rgba(59,130,246,.1);}

    .item-row .row-badge{
        position:absolute;
        top:-11px;
        left:16px;
        background:#1e293b;
        color:#fff;
        font-size:11px;
        font-weight:700;
        padding:2px 10px;
        border-radius:20px;
        letter-spacing:.4px;
    }

    /* ── Amount readonly ── */
    .amount-field{background:#f0fdf4;font-weight:700;color:#166534;}

    /* ── Summary strip ── */
    .total-strip{
        background:linear-gradient(135deg,#1e293b,#334155);
        border-radius:10px;
        padding:16px 24px;
        color:#fff;
    }
    .total-strip .lbl{font-size:12px;opacity:.7;margin-bottom:3px;}
    .total-strip .val{font-size:22px;font-weight:800;}

    /* ── Add button ── */
    #addRow{border:2px dashed #93c5fd;color:#2563eb;background:transparent;border-radius:8px;padding:8px 20px;font-weight:600;transition:.2s;}
    #addRow:hover{background:#eff6ff;}
</style>
@endsection

@section('content')
<div class="main-content">
 <div class="page-content">
  <div class="container-fluid">

    @include('common.alert')

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold">Create Invoice</h4>
            <p class="text-muted mb-0 mt-1 small">Fill in the details and add product line items.</p>
        </div>
        <a href="{{ route('store.invoice.index') }}" class="btn btn-light border">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('store.invoice.store') }}" method="POST" id="invoiceForm">
        @csrf

        {{-- ── Invoice Header Card ─────────────────────────────────────── --}}
        <div class="card page-card mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Invoice Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Showroom <span class="text-danger">*</span>
                        </label>
                        <select name="iShowroomId" class="form-select @error('iShowroomId') is-invalid @enderror" required>
                            <option value="">— Select Showroom —</option>
                            @foreach($showrooms as $s)
                                <option value="{{ $s->iShowroomId }}" {{ (string) $defaultShowroomId === (string) $s->iShowroomId ? 'selected' : '' }}>
                                  {{ $s->strShowRoomName }}
                                </option>
                            @endforeach
                        </select>
                        @error('iShowroomId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Invoice Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="InvoiceDate"
                               value="{{ old('InvoiceDate', now()->format('Y-m-d')) }}"
                               class="form-control @error('InvoiceDate') is-invalid @enderror" required>
                        @error('InvoiceDate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Notes / Remarks</label>
                        <input type="text" name="strNotes"
                               value="{{ old('strNotes') }}"
                               class="form-control" placeholder="Optional notes…">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Line Items Card ──────────────────────────────────────────── --}}
        <div class="card page-card mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-success"></i>Product Line Items</h6>
                <button type="button" id="addRow" class="btn btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Product
                </button>
            </div>

            <div class="card-body pt-4" id="itemsContainer">

                {{-- Seed existing items (after validation error) --}}
                @php
                    $oldItems = old('items', [['iCategoryId'=>'','iProductId'=>'','quantity'=>1,'unit_price'=>'','iAmount'=>'']]);
                @endphp

                @foreach($oldItems as $idx => $row)
                    <div class="item-row" id="row-{{ $idx }}">
                        <span class="row-badge">Item #<span class="rn">{{ $idx + 1 }}</span></span>

                        <div class="row g-2 align-items-end mt-1">

                            {{-- Product Category --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">
                                    Product Category <span class="text-danger">*</span>
                                </label>
                                <select name="items[{{ $idx }}][iCategoryId]"
                                        class="form-select form-select-sm cat-select" required>
                                    <option value="">— Select Category —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->iCategoryId }}"
                                            {{ (string)($row['iCategoryId'] ?? '') === (string)$cat->iCategoryId ? 'selected' : '' }}>
                                            {{ $cat->strCategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Product --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold mb-1">
                                    Product <span class="text-danger">*</span>
                                </label>
                                <select name="items[{{ $idx }}][iProductId]"
                                        class="form-select form-select-sm prod-select" required>
                                    <option value="">— Select Product —</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->iProductId }}"
                                            data-price="{{ $prod->MRP }}"
                                            {{ (string)($row['iProductId'] ?? '') === (string)$prod->iProductId ? 'selected' : '' }}>
                                            {{ $prod->strProductName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold mb-1">
                                    Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" min="1"
                                       name="items[{{ $idx }}][quantity]"
                                       value="{{ $row['quantity'] ?? 1 }}"
                                       class="form-control form-control-sm qty-input" required>
                            </div>

                            {{-- Unit Price --}}
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold mb-1">
                                    Unit Price (₹) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0"
                                       name="items[{{ $idx }}][unit_price]"
                                       value="{{ $row['unit_price'] ?? '' }}"
                                       class="form-control form-control-sm price-input"
                                       placeholder="0.00" required>
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-1">
                                <label class="form-label small fw-semibold mb-1">Amount</label>
                                <input type="text"
                                       name="items[{{ $idx }}][iAmount]"
                                       value="{{ $row['iAmount'] ?? '' }}"
                                       class="form-control form-control-sm amount-field" readonly>
                            </div>

                            {{-- Remove --}}
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100"
                                        title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>

            {{-- Total strip --}}
            <div class="card-footer bg-white border-top pt-3 pb-4 px-4">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <div class="total-strip">
                            <div class="lbl">GRAND TOTAL</div>
                            <div class="val" id="grandTotal">₹0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2 mb-5">
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-1"></i> Save Invoice
            </button>
            <a href="{{ route('store.invoice.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>

  </div>
 </div>
</div>
@endsection

@section('scripts')
@php
    $productsForJs = $products->map(function ($p) {
        return [
            'id' => $p->iProductId,
            'name' => $p->strProductName,
            'mrp' => $p->MRP,
            'cat' => $p->iCategoryId,
        ];
    })->values();
@endphp
<script>
const ALL_PRODUCTS = @json($productsForJs);
let rowIndex = {{ count($oldItems) }};   // start from existing count

// ── Helpers ──────────────────────────────────────────────────────────────────

function buildProductOptions(categoryId, selectedId) {
    let html = '<option value="">— Select Product —</option>';
    ALL_PRODUCTS.forEach(function(p) {
        if (!categoryId || String(p.cat) === String(categoryId)) {
            const sel = String(p.id) === String(selectedId) ? ' selected' : '';
            html += `<option value="${p.id}" data-price="${p.mrp}"${sel}>${p.name}</option>`;
        }
    });
    return html;
}

function calcRow(row) {
    const qty   = parseFloat($(row).find('.qty-input').val())   || 0;
    const price = parseFloat($(row).find('.price-input').val()) || 0;
    const amt   = qty * price;
    $(row).find('.amount-field').val(amt > 0 ? amt.toFixed(2) : '');
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    $('.amount-field').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#grandTotal').text('₹' + total.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}));
}

function reindex() {
    $('#itemsContainer .item-row').each(function(i) {
        $(this).attr('id', 'row-' + i);
        $(this).find('.rn').text(i + 1);
        $(this).find('[name]').each(function() {
            const n = $(this).attr('name').replace(/items\[\d+\]/, 'items[' + i + ']');
            $(this).attr('name', n);
        });
        // disable remove btn if only 1 row
        $(this).find('.remove-row').prop('disabled', $('#itemsContainer .item-row').length === 1);
    });
}

function newRow() {
    const html = `
    <div class="item-row" id="row-${rowIndex}">
        <span class="row-badge">Item #<span class="rn">${$('#itemsContainer .item-row').length + 1}</span></span>
        <div class="row g-2 align-items-end mt-1">

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Product Category <span class="text-danger">*</span></label>
                <select name="items[${rowIndex}][iCategoryId]" class="form-select form-select-sm cat-select" required>
                    <option value="">— Select Category —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->iCategoryId }}">{{ addslashes($cat->strCategoryName) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Product <span class="text-danger">*</span></label>
                <select name="items[${rowIndex}][iProductId]" class="form-select form-select-sm prod-select" required>
                    <option value="">— Select Product —</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Quantity <span class="text-danger">*</span></label>
                <input type="number" min="1" name="items[${rowIndex}][quantity]"
                       value="1" class="form-control form-control-sm qty-input" required>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Unit Price (₹) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]"
                       class="form-control form-control-sm price-input" placeholder="0.00" required>
            </div>

            <div class="col-md-1">
                <label class="form-label small fw-semibold mb-1">Amount</label>
                <input type="text" name="items[${rowIndex}][iAmount]"
                       class="form-control form-control-sm amount-field" readonly>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row w-100">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>
    </div>`;

    $('#itemsContainer').append(html);
    rowIndex++;
    reindex();
    updateGrandTotal();
}

// ── Events ────────────────────────────────────────────────────────────────────

$(document).ready(function() {

    // ── Category change → filter products ──
    $(document).on('change', '.cat-select', function() {
        const catId    = $(this).val();
        const $prodSel = $(this).closest('.item-row').find('.prod-select');
        $prodSel.html(buildProductOptions(catId, ''));
    });

    // ── Product change → prefill MRP as unit price ──
    $(document).on('change', '.prod-select', function() {
        const mrp = $(this).find(':selected').data('price') || '';
        const $row = $(this).closest('.item-row');
        if (mrp) $row.find('.price-input').val(parseFloat(mrp).toFixed(2));
        calcRow($row);
    });

    // ── Qty / price change → recalc ──
    $(document).on('input change', '.qty-input, .price-input', function() {
        calcRow($(this).closest('.item-row'));
    });

    // ── Remove row ──
    $(document).on('click', '.remove-row', function() {
        if ($('#itemsContainer .item-row').length > 1) {
            $(this).closest('.item-row').remove();
            reindex();
            updateGrandTotal();
        }
    });

    // ── Add row ──
    $('#addRow').on('click', function() { newRow(); });

    // ── Initialise existing rows (for old() repopulation) ──
    $('.cat-select').each(function() {
        const catId    = $(this).val();
        const $prodSel = $(this).closest('.item-row').find('.prod-select');
        const selProd  = $prodSel.find(':selected').val();
        $prodSel.html(buildProductOptions(catId, selProd));
    });

    updateGrandTotal();
});
</script>
@endsection
