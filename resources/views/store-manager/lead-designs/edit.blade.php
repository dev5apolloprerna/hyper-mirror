@extends('layouts.app')

@section('title', 'Edit Lead Design')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Edit Lead Design</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.leads.designs.update', [$lead->iLeadId, $design->iLeadDesignId]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="strTitle" class="form-control" value="{{ old('strTitle', $design->strTitle) }}" placeholder="Enter design title">
                                @if($errors->has('strTitle'))
                                    <span class="text-danger">
                                        {{ $errors->first('strTitle') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Design File</label>
                                <input type="file" name="strFilename" class="form-control">
                                @if($errors->has('strFilename'))
                                    <span class="text-danger">
                                        {{ $errors->first('strFilename') }}
                                    </span>
                                @endif

                                @if($design->strFilename)
                                    <div class="mt-2">
                                        <a href="{{ asset('uploads/lead-designs/' . $design->strFilename) }}" target="_blank">
                                            View Current File
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
