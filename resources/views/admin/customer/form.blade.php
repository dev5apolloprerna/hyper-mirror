@extends('layouts.app')

@section('title', isset($customer) ? 'Edit Customer' : 'Add Customer')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.customer.index') }}"
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
                            <form action="{{ isset($customer) ? route('admin.customer.update', $customer->iCustomerId) : route('admin.customer.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Customer Name <span style="color:red;">*</span></label>
                                            <input type="text" name="strCustomer" class="form-control"
                                                   value="{{ old('strCustomer', $customer->strCustomer ?? '') }}"
                                                   placeholder="Enter customer name">
                                            @if($errors->has('strCustomer'))
                                                <span class="text-danger">
                                                    {{ $errors->first('strCustomer') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Mobile <span style="color:red;">*</span></label>
                                            <input type="text" name="strMobile" class="form-control"
                                                   value="{{ old('strMobile', $customer->strMobile ?? '') }}"
                                                   placeholder="Enter mobile number" maxlength="10">
                                            @if($errors->has('strMobile'))
                                                <span class="text-danger">
                                                    {{ $errors->first('strMobile') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea name="strAddress" class="form-control" rows="4" placeholder="Enter address">{{ old('strAddress', $customer->strAddress ?? '') }}</textarea>
                                            @if($errors->has('strAddress'))
                                                <span class="text-danger">
                                                    {{ $errors->first('strAddress') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            {{ isset($customer) ? 'Update' : 'Submit' }}
                                        </button>
                                        <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">Cancel</a>
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
