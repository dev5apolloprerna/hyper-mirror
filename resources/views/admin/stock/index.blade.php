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
                        <div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#stockInModal">
                                <i class="fas fa-plus"></i> Stock In
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#stockOutModal">
                                <i class="fas fa-minus"></i> Stock Out
                            </button>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                                <i class="fas fa-exchange-alt"></i> Transfer
                            </button>
                            <a href="{{ route('admin.stock.ledger') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-history"></i> Movement Ledger
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <select name="iShowroomId" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Showrooms</option>
                                        @foreach($showrooms as $showroom)
                                            <option value="{{ $showroom->iShowroomId }}" {{ request('iShowroomId') == $showroom->iShowroomId ? 'selected' : '' }}>
                                                {{ $showroom->strShowRoomName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="iCategoryId" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->iCategoryId }}" {{ request('iCategoryId') == $category->iCategoryId ? 'selected' : '' }}>
                                                {{ $category->strCategoryName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Product">
                                        <button type="submit" class="btn btn-success btn-sm me-1"><i class="fas fa-search"></i></button>
                                        <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Showroom</th>
                                            <th>Quantity in Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stocks as $stock)
                                            <tr>
                                                <td>{{ $stock->product->strProductName ?? '' }}</td>
                                                <td>{{ $stock->product->category->strCategoryName ?? '' }}</td>
                                                <td>{{ $stock->showroom->strShowRoomName ?? '' }}</td>
                                                <td>
                                                    <span class="badge {{ $stock->iQuantity <= 0 ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $stock->iQuantity }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No stock records found. Use "Stock In" to add stock.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $stocks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Stock In Modal --}}
<div class="modal fade" id="stockInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.stock.in') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span style="color:red;">*</span></label>
                        <select name="iProductId" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->iProductId }}">{{ $product->strProductName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Showroom <span style="color:red;">*</span></label>
                        <select name="iShowroomId" class="form-control" required>
                            <option value="">Select Showroom</option>
                            @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span style="color:red;">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason / Notes</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Purchase order #123" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Stock</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Stock Out Modal --}}
<div class="modal fade" id="stockOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.stock.out') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span style="color:red;">*</span></label>
                        <select name="iProductId" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->iProductId }}">{{ $product->strProductName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Showroom <span style="color:red;">*</span></label>
                        <select name="iShowroomId" class="form-control" required>
                            <option value="">Select Showroom</option>
                            @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span style="color:red;">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason / Notes</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Damaged goods" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Remove Stock</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Transfer Modal --}}
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.stock.transfer') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Stock Between Showrooms</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span style="color:red;">*</span></label>
                        <select name="iProductId" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->iProductId }}">{{ $product->strProductName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">From Showroom <span style="color:red;">*</span></label>
                        <select name="iFromShowroomId" class="form-control" required>
                            <option value="">Select Showroom</option>
                            @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To Showroom <span style="color:red;">*</span></label>
                        <select name="iToShowroomId" class="form-control" required>
                            <option value="">Select Showroom</option>
                            @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span style="color:red;">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason / Notes</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Rebalancing showroom stock" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info text-white">Transfer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
