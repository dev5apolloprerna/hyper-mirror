@extends('layouts.app')
@section('title', 'Quotation History')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Quotation History - {{ $lead->strLeadNo }}</h4>
        <a href="{{ route('admin.reports.leads.show', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card mb-3"><div class="card-body py-2">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.reports.leads.quotations', $lead->iLeadId) }}">
            <div class="col-md-3">
                <label class="form-label mb-1 small">Quotation Batch</label>
                <select class="form-select form-select-sm" name="batch_id">
                    <option value="">All</option>
                    @foreach($batchOptions as $batchId)
                        <option value="{{ $batchId }}" {{ (string)request('batch_id') === (string)$batchId ? 'selected' : '' }}>{{ $batchId }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success btn-sm">Search</button>
                <a href="{{ route('admin.reports.leads.quotations', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive"><table class="table table-bordered mb-0">
        <thead class="table-light"><tr><th>#</th><th>Batch</th><th>Category</th><th>Product</th><th>Shape</th><th>Feature</th><th>Qty</th><th>Sqft</th><th>Rate</th><th>Amount</th></tr></thead>
        <tbody>
            @forelse($quotations as $quotation)
                <tr>
                    <td>{{ $quotations->firstItem() + $loop->index }}</td>
                    <td>{{ $quotation->quotation_batch_id ?? '—' }}</td>
                    <td>{{ optional($quotation->category)->strCategoryName ?? '—' }}</td>
                    <td>{{ optional($quotation->product)->strProductName ?? '—' }}</td>
                    <td>{{ optional($quotation->shape)->shape_name ?? '—' }}</td>
                    <td>{{ optional($quotation->feature)->feature_name ?? '—' }}</td>
                    <td>{{ $quotation->quantity ?? 0 }}</td>
                    <td>{{ number_format((float)($quotation->decTotalSqft ?? 0), 2) }}</td>
                    <td>₹{{ number_format((float)($quotation->decRatePerSqft ?? 0), 2) }}</td>
                    <td>₹{{ number_format((float)($quotation->iAmount ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No quotations found.</td></tr>
            @endforelse
        </tbody>
    </table></div><div class="p-3">{{ $quotations->links() }}</div></div>
</div></div></div>
@endsection
