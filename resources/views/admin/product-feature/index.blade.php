@extends('layouts.app')

@section('title', 'Product Feature')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Product Feature</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Product Feature</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.product-feature.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Feature Name <span style="color:red;">*</span></label>
                                    <input type="text" name="feature_name" class="form-control" value="{{ old('feature_name') }}" placeholder="Enter feature name">
                                    @if($errors->has('feature_name'))
                                        <span class="text-danger">
                                            {{ $errors->first('feature_name') }}
                                        </span>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

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
                                    <form method="GET" action="{{ route('admin.product-feature.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Feature" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.product-feature.index') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                            <th>Feature Name</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($features as $feature)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $feature->feature_id }}">
                                                </td>
                                                <td>{{ $feature->feature_name }}</td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="text-primary me-2 edit-feature-btn"
                                                       title="Edit"
                                                       data-id="{{ $feature->feature_id }}"
                                                       data-name="{{ $feature->feature_name }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);"
                                                       class="text-danger delete-record"
                                                       data-id="{{ $feature->feature_id }}"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $feature->feature_id }}" action="{{ route('admin.product-feature.delete', $feature->feature_id) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $features->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-labelledby="editFeatureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editFeatureForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFeatureModalLabel">Edit Product Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Feature Name <span style="color:red;">*</span></label>
                        <input type="text" name="feature_name" id="edit_feature_name" class="form-control" placeholder="Enter feature name">
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
                url: "{{ route('admin.product-feature.bulkDelete') }}",
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

    $('.edit-feature-btn').on('click', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#edit_feature_name').val(name);
        $('#editFeatureForm').attr('action', "{{ url('admin/product-feature/update') }}/" + id);

        $('#editFeatureModal').modal('show');
    });
});
</script>
@endsection
