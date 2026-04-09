@extends('layouts.app')
@section('title', $isEdit ? 'Edit CRM User' : 'Add CRM User')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">{{ $isEdit ? 'Edit User' : 'Add User' }}</h4>
                    <p class="text-muted mb-0 mt-1">{{ $isEdit ? 'Update user details and showroom access.' : 'Create a new CRM user profile.' }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light border">
                    <i class="fas fa-arrow-left me-1"></i> Back to Listing
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">User Name <span class="text-danger">*</span></label>
                                <input type="text" name="strUserName" value="{{ old('strUserName', $user->strUserName ?: $user->first_name) }}" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="strUserMobile" value="{{ old('strUserMobile', $user->strUserMobile ?: $user->mobile_number) }}" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">User Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="iRoalId" class="form-select" required>
                                    <option value="">Select role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->iRoleId }}" @selected(old('iRoalId', $user->iRoalId) == $role->iRoleId)>
                                            {{ $role->strRole }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Can View Financial Data? <span class="text-danger">*</span></label>
                                <select name="can_view_financial" class="form-select" required>
                                    <option value="0" @selected((string)old('can_view_financial', (int)$user->can_view_financial) === '0')>No — Hide prices in quotation PDF</option>
                                    <option value="1" @selected((string)old('can_view_financial', (int)$user->can_view_financial) === '1')>Yes — Show full quotation with prices</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                @if($isEdit)
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @checked((int)old('status', $user->status) === 1)>
                                        <label class="form-check-label" for="status">Active User</label>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Assign Showrooms <span class="text-danger">*</span></label>
                                @php
                                    $selectedShowrooms = old('showrooms', $isEdit ? $user->showrooms->pluck('iShowroomId')->all() : []);
                                @endphp
                                <select name="showrooms[]" class="form-select" multiple required size="6">
                                    @foreach ($showrooms as $showroom)
                                        <option value="{{ $showroom->iShowroomId }}" @selected(in_array($showroom->iShowroomId, $selectedShowrooms))>
                                            {{ $showroom->strShowRoomName }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple showrooms.</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="strUserAddress" class="form-control" rows="3">{{ old('strUserAddress', $user->strUserAddress) }}</textarea>
                            </div>

                            @if(!$isEdit)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update User' : 'Create User' }}</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- @if($isEdit)
            <div class="card border-0 shadow-sm mt-3" id="change-password-card">
                <div class="card-header bg-white">
                        <h6 class="mb-0">Change Password</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.password.update', $user) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-warning text-white w-100">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif -->

        </div>
    </div>
</div>
@endsection
