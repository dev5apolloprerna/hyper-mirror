@extends('layouts.app')

@section('title', 'User Showroom')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">User Showroom</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Side Add Form --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add User Showroom</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.user-showroom.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">User <span style="color:red;">*</span></label>
                                    <select name="UserId" class="form-control">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('UserId') == $user->id ? 'selected' : '' }}>
                                                {{ $user->strUserName ?: ($user->first_name . ' ' . $user->last_name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('UserId'))
                                        <span class="text-danger">
                                            {{ $errors->first('UserId') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Showroom <span style="color:red;">*</span></label>
                                    <select name="ShowRoomId" class="form-control">
                                        <option value="">Select Showroom</option>
                                        @foreach($showrooms as $showroom)
                                            <option value="{{ $showroom->iShowroomId }}" {{ old('ShowRoomId') == $showroom->iShowroomId ? 'selected' : '' }}>
                                                {{ $showroom->strShowRoomName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('ShowRoomId'))
                                        <span class="text-danger">
                                            {{ $errors->first('ShowRoomId') }}
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
                                    <form method="GET" action="{{ route('admin.user-showroom.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search User / Showroom" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.user-showroom.index') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                            <th>User</th>
                                            <th>Showroom</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($userShowrooms as $row)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $row->UserShowRoomId }}">
                                                </td>
                                                <td>{{ $row->user->strUserName ?: (($row->user->first_name ?? '') . ' ' . ($row->user->last_name ?? '')) }}</td>
                                                <td>{{ $row->showroom->strShowRoomName ?? '' }}</td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="text-primary me-2 edit-user-showroom-btn"
                                                       title="Edit"
                                                       data-id="{{ $row->UserShowRoomId }}"
                                                       data-userid="{{ $row->UserId }}"
                                                       data-showroomid="{{ $row->ShowRoomId }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);"
                                                       class="text-danger delete-record"
                                                       data-id="{{ $row->UserShowRoomId }}"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $row->UserShowRoomId }}" action="{{ route('admin.user-showroom.delete', $row->UserShowRoomId) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $userShowrooms->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editUserShowroomModal" tabindex="-1" aria-labelledby="editUserShowroomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editUserShowroomForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserShowroomModalLabel">Edit User Showroom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User <span style="color:red;">*</span></label>
                        <select name="UserId" id="edit_UserId" class="form-control">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->strUserName ?: ($user->first_name . ' ' . $user->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Showroom <span style="color:red;">*</span></label>
                        <select name="ShowRoomId" id="edit_ShowRoomId" class="form-control">
                            <option value="">Select Showroom</option>
                            @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->iShowroomId }}">
                                    {{ $showroom->strShowRoomName }}
                                </option>
                            @endforeach
                        </select>
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
                url: "{{ route('admin.user-showroom.bulkDelete') }}",
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

    $('.edit-user-showroom-btn').on('click', function () {
        let id = $(this).data('id');
        let userId = $(this).data('userid');
        let showroomId = $(this).data('showroomid');

        $('#edit_UserId').val(userId);
        $('#edit_ShowRoomId').val(showroomId);
        $('#editUserShowroomForm').attr('action', "{{ url('admin/user-showroom/update') }}/" + id);

        $('#editUserShowroomModal').modal('show');
    });
});
</script>
@endsection