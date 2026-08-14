@extends('layouts.app')

@section('title', 'Stock Movement Ledger')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Stock Movement Ledger</h4>
                        <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Stock
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" action="{{ route('admin.stock.ledger') }}" class="row g-2 align-items-center">
                                <div class="col-md-2">
                                    <select name="iProductId" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Products</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->iProductId }}" {{ request('iProductId') == $product->iProductId ? 'selected' : '' }}>
                                                {{ $product->strProductName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="iShowroomId" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Showrooms</option>
                                        @foreach($showrooms as $showroom)
                                            <option value="{{ $showroom->iShowroomId }}" {{ request('iShowroomId') == $showroom->iShowroomId ? 'selected' : '' }}>
                                                {{ $showroom->strShowRoomName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="strType" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Types</option>
                                        @foreach(\App\Enums\StockMovementType::labels() as $value => $label)
                                            <option value="{{ $value }}" {{ request('strType') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="from" class="form-control" value="{{ request('from') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="to" class="form-control" value="{{ request('to') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.stock.ledger') }}" class="btn btn-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Product</th>
                                            <th>Showroom</th>
                                            <th>Type</th>
                                            <th>Qty</th>
                                            <th>Balance After</th>
                                            <th>Reference</th>
                                            <th>Reason</th>
                                            <th>By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($movements as $movement)
                                            <tr>
                                                <td>{{ $movement->created_at->format('d-M-Y h:i A') }}</td>
                                                <td>{{ $movement->product->strProductName ?? '' }}</td>
                                                <td>
                                                    {{ $movement->showroom->strShowRoomName ?? '' }}
                                                    @if($movement->relatedShowroom)
                                                        <br><small class="text-muted">
                                                            {{ str_contains($movement->strType, 'transfer_out') ? '→ ' : '← ' }}{{ $movement->relatedShowroom->strShowRoomName }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td><span class="badge {{ $movement->badge_class }}">{{ $movement->label }}</span></td>
                                                <td>{{ \App\Enums\StockMovementType::isOutbound($movement->strType) ? '-' : '+' }}{{ $movement->iQuantity }}</td>
                                                <td>{{ $movement->iBalanceAfter }}</td>
                                                <td>
                                                    @if($movement->strReferenceType === 'invoice')
                                                        Invoice #{{ $movement->iReferenceId }}
                                                    @endif
                                                </td>
                                                <td>{{ $movement->strReason }}</td>
                                                <td>{{ trim(($movement->createdBy->first_name ?? '') . ' ' . ($movement->createdBy->last_name ?? '')) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No stock movements found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $movements->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
