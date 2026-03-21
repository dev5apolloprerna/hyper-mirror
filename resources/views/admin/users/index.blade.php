@extends('layouts.app')
@section('title', 'CRM Users')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Admin Users</h4>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Create User</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.users.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" name="strUserName" value="{{ old('strUserName') }}" class="form-control" placeholder="Enter user name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" name="strUserMobile" value="{{ old('strUserMobile') }}" class="form-control" placeholder="Enter mobile number" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">User Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Enter user email" required>
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="iRoalId" class="form-select" required>
                                        <option value="">Select role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->iRoleId }}" @selected(old('iRoalId') == $role->iRoleId)>
                                                {{ $role->strRole }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assign Showrooms</label>
                                    <select name="showrooms[]" id="create-showroom-select2" class="form-select showroom-select2" multiple required>
                                        @foreach ($showrooms as $showroom)
                                            <option value="{{ $showroom->iShowroomId }}" @selected(collect(old('showrooms', []))->contains($showroom->iShowroomId))>
                                                {{ $showroom->strShowRoomName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Search and select multiple showrooms using the checkbox list.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="strUserAddress" class="form-control" rows="3" placeholder="Enter address">{{ old('strUserAddress') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h5 class="card-title mb-0">User List</h5>
                            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search users, roles, showrooms">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Showrooms</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                                <td>{{ $user->strUserName ?: $user->first_name }}</td>
                                                <td>{{ $user->strUserMobile ?: $user->mobile_number }}</td>
                                                <td>{{ $user->email ?: $user->email }}</td>
                                                <td>{{ optional($user->crmRole)->strRole ?? '-' }}</td>
                                                <td>
                                                    @forelse ($user->showrooms as $showroom)
                                                        <span class="badge bg-primary-subtle text-primary me-1 mb-1">{{ $showroom->strShowRoomName }}</span>
                                                    @empty
                                                        <span class="text-muted">No showroom assigned</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @if ((int) $user->status === 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-info text-white mb-1 edit-user-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal"
                                                        data-action="{{ route('admin.users.update', $user) }}"
                                                        data-name="{{ $user->strUserName ?: $user->first_name }}"
                                                        data-mobile="{{ $user->strUserMobile ?: $user->mobile_number }}"
                                                        data-email="{{ $user->email ?: $user->email }}"
                                                        data-role="{{ $user->iRoalId }}"
                                                        data-address="{{ $user->strUserAddress }}"
                                                        data-status="{{ (int) $user->status }}"
                                                        data-showrooms='@json($user->showrooms->pluck("iShowroomId")->all())'>
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-warning text-white mb-1 password-user-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#passwordModal"
                                                        data-action="{{ route('admin.users.password.update', $user) }}"
                                                        data-username="{{ $user->strUserName ?: $user->first_name }}">
                                                        Change Password
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mb-1" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No admin users found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="editUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User Name</label>
                            <input type="text" name="strUserName" id="edit_strUserName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="strUserMobile" id="edit_strUserMobile" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="iRoalId" id="edit_iRoalId" class="form-select" required>
                                <option value="">Select role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->iRoleId }}">{{ $role->strRole }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="edit_status">
                                <label class="form-check-label" for="edit_status">Active user</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Assign Showrooms</label>
                            <select name="showrooms[]" id="edit_showrooms" class="form-select showroom-select2" multiple required data-dropdown-parent="#editUserModal">
                                @foreach ($showrooms as $showroom)
                                    <option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="strUserAddress" id="edit_strUserAddress" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="passwordForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3 text-muted">Update password for <strong id="password_user_name"></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-results__option {
            padding: 0;
        }

        .select2-container--default .showroom-option .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            margin: 0;
        }

        .select2-container--default .showroom-option .form-check-input {
            margin-top: 0;
            pointer-events: none;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            padding: 0.35rem;
        }
    </style>

    <script>
        $(function () {
            function showroomTemplate(option) {
                if (!option.id) {
                    return option.text;
                }

                const isSelected = option.element && option.element.selected;
                const $option = $(
                    '<div class="showroom-option">' +
                        '<div class="form-check">' +
                            '<input type="checkbox" class="form-check-input" ' + (isSelected ? 'checked' : '') + '>' +
                            '<label class="form-check-label"></label>' +
                        '</div>' +
                    '</div>'
                );

                $option.find('.form-check-label').text(option.text);
                return $option;
            }

            $('.showroom-select2').each(function () {
                const $select = $(this);
                const parentSelector = $select.data('dropdown-parent');

                $select.select2({
                    placeholder: 'Select showroom',
                    closeOnSelect: false,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: parentSelector ? $(parentSelector) : null,
                    templateResult: showroomTemplate,
                    templateSelection: function (option) {
                        return option.text || option.id;
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            });

            $('.edit-user-btn').on('click', function () {
                const button = $(this);
                const selectedShowrooms = button.data('showrooms') || [];

                $('#editUserForm').attr('action', button.data('action'));
                $('#edit_strUserName').val(button.data('name'));
                $('#edit_strUserMobile').val(button.data('mobile'));
                $('#edit_iRoalId').val(String(button.data('role')));
                $('#edit_strUserAddress').val(button.data('address'));
                $('#edit_status').prop('checked', Number(button.data('status')) === 1);
                $('#edit_showrooms').val(selectedShowrooms).trigger('change');
            });

            $('.password-user-btn').on('click', function () {
                const button = $(this);
                $('#passwordForm').attr('action', button.data('action'));
                $('#password_user_name').text(button.data('username'));
                $('#passwordForm')[0].reset();
            });
        });
    </script>
 @endsection
