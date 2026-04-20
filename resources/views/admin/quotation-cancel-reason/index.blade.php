@extends('layouts.app')

@section('title', 'Quotation Cancel Reason')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Quotation Cancel Reason</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Cancel Reason</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.quotation-cancel-reason.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Reason <span style="color:red;">*</span></label>
                                    <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="Enter cancel reason">
                                    @if($errors->has('reason'))
                                        <span class="text-danger">{{ $errors->first('reason') }}</span>
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
                                    <form method="GET" action="{{ route('admin.quotation-cancel-reason.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Reason" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1"><i class="fas fa-search"></i></button>
                                        <a href="{{ route('admin.quotation-cancel-reason.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th width="5%"><input type="checkbox" id="selectAll"></th>
                                            <th>Reason</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reasons as $reason)
                                            <tr>
                                                <td><input type="checkbox" class="record-checkbox" value="{{ $reason->id }}"></td>
                                                <td>{{ $reason->reason }}</td>
                                                <td>
                                                    <a href="javascript:void(0);" class="text-primary me-2 edit-reason-btn" title="Edit" data-id="{{ $reason->id }}" data-reason="{{ $reason->reason }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $reason->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $reason->id }}" action="{{ route('admin.quotation-cancel-reason.delete', $reason->id) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center">No records found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">{{ $reasons->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editReasonModal" tabindex="-1" aria-labelledby="editReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editReasonForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReasonModalLabel">Edit Cancel Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason <span style="color:red;">*</span></label>
                        <input type="text" name="reason" id="edit_reason" class="form-control" placeholder="Enter cancel reason">
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
                url: "{{ route('admin.quotation-cancel-reason.bulkDelete') }}",
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

    $('.edit-reason-btn').on('click', function () {
        let id = $(this).data('id');
        let reason = $(this).data('reason');

        $('#edit_reason').val(reason);
        $('#editReasonForm').attr('action', "{{ url('admin/quotation-cancel-reason/update') }}/" + id);
        $('#editReasonModal').modal('show');
    });
});
</script>
@endsection
