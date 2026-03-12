@extends('layouts.app')

@section('title', isset($userShowroom) ? 'Edit User Showroom' : 'Add User Showroom')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ isset($userShowroom) ? 'Edit User Showroom' : 'Add User Showroom' }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.user-showroom.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($userShowroom) ? route('admin.user-showroom.update', $userShowroom->UserShowRoomId) : route('admin.user-showroom.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">User <span style="color:red;">*</span></label>
                                <select name="UserId" class="form-control">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('UserId', $userShowroom->UserId ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('UserId'))
                                    <span class="text-danger">
                                        {{ $errors->first('UserId') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Showroom <span style="color:red;">*</span></label>
                                <select name="ShowRoomId" class="form-control">
                                    <option value="">Select Showroom</option>
                                    @foreach($showrooms as $showroom)
                                        <option value="{{ $showroom->iShowroomId }}" {{ old('ShowRoomId', $userShowroom->ShowRoomId ?? '') == $showroom->iShowroomId ? 'selected' : '' }}>
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

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($userShowroom) ? 'Update' : 'Submit' }}
                                </button>
                                <a href="{{ route('admin.user-showroom.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
