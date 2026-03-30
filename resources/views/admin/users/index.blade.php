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
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info text-white">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>

                                                <form method="POST"
                                                      action="{{ route('admin.users.destroy', $user) }}"
                                                      class="d-inline-block"
                                                      onsubmit="return confirm('Delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash me-1"></i> Delete
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
@endsection