@extends('layouts.app')

@section('title', 'User Showroom List')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">User Showroom List</h4>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                <i class="fas fa-trash"></i> Bulk Delete
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.user-showroom.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('admin.user-showroom.index') }}" class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Search User Showroom</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter user name or showroom name">
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="submit" class="btn btn-success btn-sm mt-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.user-showroom.index') }}" class="btn btn-secondary btn-sm mt-2">Reset</a>
                        </div>
                    </form>

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
                                        <td>{{ $row->user->name ?? '' }}</td>
                                        <td>{{ $row->showroom->strShowRoomName ?? '' }}</td>
                                        <td>
                                            <a href="{{ route('admin.user-showroom.edit', $row->UserShowRoomId) }}" class="text-primary me-2" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $row->UserShowRoomId }}" title="Delete">
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
});
</script>
@endsection
