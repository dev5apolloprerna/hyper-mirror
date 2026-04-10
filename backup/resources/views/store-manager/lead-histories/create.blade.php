@extends('layouts.app')

@section('title', 'Add Lead History')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Add Lead History</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('store.leads.histories.store', $lead->iLeadId) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Status <span style="color:red;">*</span></label>
                                <input type="text" name="iStatus" class="form-control" value="{{ old('iStatus') }}" placeholder="Enter status">
                                @if($errors->has('iStatus'))
                                    <span class="text-danger">
                                        {{ $errors->first('iStatus') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Next Follow Up Date</label>
                                <input type="date" name="NetFolloupwdate" class="form-control" value="{{ old('NetFolloupwdate') }}">
                                @if($errors->has('NetFolloupwdate'))
                                    <span class="text-danger">
                                        {{ $errors->first('NetFolloupwdate') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Comments</label>
                                <textarea name="strComments" class="form-control" rows="4" placeholder="Enter comments">{{ old('strComments') }}</textarea>
                                @if($errors->has('strComments'))
                                    <span class="text-danger">
                                        {{ $errors->first('strComments') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection