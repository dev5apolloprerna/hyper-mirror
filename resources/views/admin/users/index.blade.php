@extends('layouts.app')

@section('title', 'CRM Users')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <!-- Page Title -->
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-sm-0">User Master</h4>
                    <p class="text-muted mb-0 mt-1">Manage CRM users, roles and showroom assignments.</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add User
                    </a>
                </div>
            </div>

            <!-- Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                    <div>
                        <h5 class="card-title mb-0">User Listing</h5>
                        <small class="text-muted">View and manage all CRM users</small>
                    </div>

                    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-wrap gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search user / role / showroom"
                            style="min-width: 260px;"
                        >
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Financial</th>
                                    <th>Showrooms</th>
                                    <th>Status</th>
                                    <th class="text-center" width="160">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $loop->index }}</td>

                                        <td>
                                            <div class="fw-semibold text-dark">
                                                {{ $user->strUserName ?: $user->first_name ?: '-' }}
                                            </div>
                                        </td>

                                        <td>{{ $user->strUserMobile ?: $user->mobile_number ?: '-' }}</td>

                                        <td>
                                            <span class="text-muted">{{ $user->email ?: '-' }}</span>
                                        </td>

                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ optional($user->crmRole)->strRole ?? '-' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($user->can_view_financial)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>

                                        <td>
                                            @forelse ($user->showrooms as $showroom)
                                                <span class="badge bg-primary-subtle text-primary me-1 mb-1">
                                                    {{ $showroom->strShowRoomName }}
                                                </span>
                                            @empty
                                                <span class="text-muted">None</span>
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
                                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info text-white" title="Edit">
                                                    <i class="fas fa-edit me-1"></i> 
                                                </a>
                                                 <button type="button" title="Change Password" 
                                                        class="btn btn-sm btn-warning text-white js-open-password-modal"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#changePasswordModal"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->strUserName ?: $user->first_name ?: 'User' }}">
                                                    <i class="fas fa-key me-1"></i></button>

                                                <form method="POST"
                                                      action="{{ route('admin.users.destroy', $user) }}"
                                                      class="d-inline-block"
                                                      onsubmit="return confirm('Delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete" 
                                                            class="btn btn-sm btn-danger"
                                                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash me-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="mt-4 d-flex justify-content-end">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="changePasswordForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Update password for <strong id="passwordModalUserName">User</strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.js-open-password-modal');
        const form = document.getElementById('changePasswordForm');
        const userName = document.getElementById('passwordModalUserName');
        const routeTemplate = @json(route('admin.users.password.update', ['user' => '__USER_ID__']));

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const id = button.getAttribute('data-user-id');
                const name = button.getAttribute('data-user-name') || 'User';

                form.setAttribute('action', routeTemplate.replace('__USER_ID__', id));
                userName.textContent = name;
            });
        });
    });
</script>
@endsection