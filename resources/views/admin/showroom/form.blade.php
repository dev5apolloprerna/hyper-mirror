@extends('layouts.app')

@section('title', isset($showroom) ? 'Edit Showroom' : 'Add Showroom')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ isset($showroom) ? 'Edit Showroom' : 'Add Showroom' }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.showroom.index') }}"
                               class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ isset($showroom) ? route('admin.showroom.update', $showroom->iShowroomId) : route('admin.showroom.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Showroom Name <span style="color:red;">*</span></label>
                                            <input type="text" name="strShowRoomName" class="form-control"
                                                   value="{{ old('strShowRoomName', $showroom->strShowRoomName ?? '') }}"
                                                   placeholder="Enter showroom name">
                                            @if($errors->has('strShowRoomName'))
                                                <span class="text-danger">
                                                    {{ $errors->first('strShowRoomName') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            {{ isset($showroom) ? 'Update' : 'Submit' }}
                                        </button>
                                        <a href="{{ route('admin.showroom.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
