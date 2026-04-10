@extends('layouts.app')

@section('title', 'Showroom')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Showroom</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Side Add Form --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Showroom</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.showroom.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Showroom Name <span style="color:red;">*</span></label>
                                    <input type="text" name="strShowRoomName" class="form-control" value="{{ old('strShowRoomName') }}" placeholder="Enter showroom name">
                                    @if($errors->has('strShowRoomName'))
                                        <span class="text-danger">
                                            {{ $errors->first('strShowRoomName') }}
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
                                    <form method="GET" action="{{ route('admin.showroom.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Showroom" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.showroom.index') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                            <th>Showroom Name</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($showrooms as $showroom)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $showroom->iShowroomId }}">
                                                </td>
                                                <td>{{ $showroom->strShowRoomName }}</td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="text-primary me-2 edit-showroom-btn"
                                                       title="Edit"
                                                       data-id="{{ $showroom->iShowroomId }}"
                                                       data-name="{{ $showroom->strShowRoomName }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);"
                                                       class="text-danger delete-record"
                                                       data-id="{{ $showroom->iShowroomId }}"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $showroom->iShowroomId }}" action="{{ route('admin.showroom.delete', $showroom->iShowroomId) }}" method="POST" style="display:none;">
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
                                {{ $showrooms->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editShowroomModal" tabindex="-1" aria-labelledby="editShowroomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editShowroomForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editShowroomModalLabel">Edit Showroom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Showroom Name <span style="color:red;">*</span></label>
                        <input type="text" name="strShowRoomName" id="edit_strShowRoomName" class="form-control" placeholder="Enter showroom name">
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
                url: "{{ route('admin.showroom.bulkDelete') }}",
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

    $('.edit-showroom-btn').on('click', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#edit_strShowRoomName').val(name);
        $('#editShowroomForm').attr('action', "{{ url('admin/showroom/update') }}/" + id);

        $('#editShowroomModal').modal('show');
    });
});
</script>
@endsection