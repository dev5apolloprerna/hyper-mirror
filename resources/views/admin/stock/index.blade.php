@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Stock Management</h4>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-muted mb-1">Inside Stock</p><h4>{{ $totals['inside'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-muted mb-1">Showroom Stock</p><h4>{{ $totals['showroom'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-muted mb-1">Sales</p><h4>{{ $totals['sales'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-muted mb-1">Available</p><h4>{{ $totals['available'] }}</h4></div></div></div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Add / Receive Stock</h5></div>
                        <div class="card-body">
                            <form action="{{ route('admin.stock.store') }}" method="POST">
                                @csrf
                                @include('admin.stock.partials.form', ['stock' => null])
                                <button type="submit" class="btn btn-primary">Save Stock</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-2">
                                <div class="col-md-5"><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search product, category, showroom"></div>
                                <div class="col-md-4"><select name="iShowroomId" class="form-control"><option value="">All Showrooms</option>@foreach($showrooms as $showroom)<option value="{{ $showroom->iShowroomId }}" @selected(request('iShowroomId') == $showroom->iShowroomId)>{{ $showroom->strShowRoomName }}</option>@endforeach</select></div>
                                <div class="col-md-3"><button class="btn btn-success btn-sm"><i class="fas fa-search"></i></button> <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary btn-sm">Reset</a></div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead><tr><th>Product</th><th>Category</th><th>Showroom</th><th>Inside</th><th>Showroom</th><th>Sales</th><th>Available</th><th>Status</th><th>Action</th></tr></thead>
                                    <tbody>
                                        @forelse($stocks as $stock)
                                            @php
                                                $sold = (int) optional($salesByStock->get($stock->iProductId . '-' . $stock->iShowroomId))->sold_quantity;
                                                $available = $stock->inside_quantity + $stock->showroom_quantity - $sold;
                                            @endphp
                                            <tr>
                                                <td>{{ $stock->product->strProductName ?? '' }}</td><td>{{ $stock->product->category->strCategoryName ?? '' }}</td><td>{{ $stock->showroom->strShowRoomName ?? '' }}</td>
                                                <td>{{ $stock->inside_quantity }}</td><td>{{ $stock->showroom_quantity }}</td><td>{{ $sold }}</td><td>{{ $available }}</td>
                                                <td>@if($available <= $stock->minimum_quantity)<span class="badge bg-danger">Low Stock</span>@else<span class="badge bg-success">OK</span>@endif</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary edit-stock" data-id="{{ $stock->iProductStockId }}" data-product="{{ $stock->iProductId }}" data-showroom="{{ $stock->iShowroomId }}" data-inside="{{ $stock->inside_quantity }}" data-room="{{ $stock->showroom_quantity }}" data-min="{{ $stock->minimum_quantity }}" data-remarks="{{ $stock->remarks }}">Edit</button>
                                                    <form action="{{ route('admin.stock.delete', $stock) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this stock record?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                                                </td>
                                            </tr>
                                        @empty<tr><td colspan="9" class="text-center">No stock records found.</td></tr>@endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">{{ $stocks->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStockModal" tabindex="-1"><div class="modal-dialog"><form method="POST" id="editStockForm">@csrf<div class="modal-content"><div class="modal-header"><h5 class="modal-title">Edit Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">@include('admin.stock.partials.form', ['stock' => null, 'prefix' => 'edit_'])</div><div class="modal-footer"><button class="btn btn-primary">Update</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div></div></form></div></div>
@endsection

@section('scripts')
<script>
$(function(){ $('.edit-stock').on('click', function(){ let b=$(this); $('#editStockForm').attr('action', "{{ url('admin/stock/update') }}/"+b.data('id')); $('#edit_iProductId').val(b.data('product')); $('#edit_iShowroomId').val(b.data('showroom')); $('#edit_inside_quantity').val(b.data('inside')); $('#edit_showroom_quantity').val(b.data('room')); $('#edit_minimum_quantity').val(b.data('min')); $('#edit_remarks').val(b.data('remarks')); $('#editStockModal').modal('show'); }); });
</script>
@endsection
