@extends('layouts.app')

@section('title', 'Product')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Product</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Side Add Form --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Product</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.product.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Category <span style="color:red;">*</span></label>
                                    <select name="iCategoryId" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->iCategoryId }}" {{ old('iCategoryId') == $category->iCategoryId ? 'selected' : '' }}>
                                                {{ $category->strCategoryName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('iCategoryId'))
                                        <span class="text-danger">
                                            {{ $errors->first('iCategoryId') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Product Name <span style="color:red;">*</span></label>
                                    <input type="text" name="strProductName" class="form-control" value="{{ old('strProductName') }}" placeholder="Enter product name">
                                    @if($errors->has('strProductName'))
                                        <span class="text-danger">
                                            {{ $errors->first('strProductName') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">MRP <span style="color:red;">*</span></label>
                                    <input type="number" name="MRP" class="form-control" value="{{ old('MRP', 0) }}" min="0" placeholder="Enter MRP">
                                    @if($errors->has('MRP'))
                                        <span class="text-danger">
                                            {{ $errors->first('MRP') }}
                                        </span>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Side Listing --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                        <i class="fas fa-trash"></i> Bulk Delete
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('admin.product.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Product" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>Category</th>
                                            <th>Product Name</th>
                                            <th>MRP</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $product)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $product->iProductId }}">
                                                </td>
                                                <td>{{ $product->category->strCategoryName ?? '' }}</td>
                                                <td>{{ $product->strProductName }}</td>
                                                <td>{{ $product->MRP }}</td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="text-primary me-2 edit-product-btn"
                                                       title="Edit"
                                                       data-id="{{ $product->iProductId }}"
                                                       data-category="{{ $product->iCategoryId }}"
                                                       data-name="{{ $product->strProductName }}"
                                                       data-mrp="{{ $product->MRP }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);"
                                                       class="text-danger delete-record"
                                                       data-id="{{ $product->iProductId }}"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $product->iProductId }}" action="{{ route('admin.product.delete', $product->iProductId) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editProductForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span style="color:red;">*</span></label>
                        <select name="iCategoryId" id="edit_iCategoryId" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->iCategoryId }}">
                                    {{ $category->strCategoryName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Name <span style="color:red;">*</span></label>
                        <input type="text" name="strProductName" id="edit_strProductName" class="form-control" placeholder="Enter product name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">MRP <span style="color:red;">*</span></label>
                        <input type="number" name="MRP" id="edit_MRP" class="form-control" min="0" placeholder="Enter MRP">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('#selectAll').on('click', function () {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.delete-record').on('click', function () {
        let id = $(this).data('id');

        if (confirm('Are you sure you want to delete this record?')) {
            $('#delete-form-' + id).submit();
        }
    });

    $('#bulkDeleteBtn').on('click', function () {
        let ids = [];

        $('.record-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete selected records?')) {
            $.ajax({
                url: "{{ route('admin.product.bulkDelete') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    });

    $('.edit-product-btn').on('click', function () {
        let id = $(this).data('id');
        let category = $(this).data('category');
        let name = $(this).data('name');
        let mrp = $(this).data('mrp');

        $('#edit_iCategoryId').val(category);
        $('#edit_strProductName').val(name);
        $('#edit_MRP').val(mrp);
        $('#editProductForm').attr('action', "{{ url('admin/product/update') }}/" + id);

        $('#editProductModal').modal('show');
    });
});
</script>
@endsection