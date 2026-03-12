@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.product.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($product) ? route('admin.product.update', $product->iProductId) : route('admin.product.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Category <span style="color:red;">*</span></label>
                                <select name="iCategoryId" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->iCategoryId }}" {{ old('iCategoryId', $product->iCategoryId ?? '') == $category->iCategoryId ? 'selected' : '' }}>
                                            {{ $category->strCategoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('iCategoryId'))
                                    <span class="text-danger">
                                        {{ $errors->first('iCategoryId') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Product Name <span style="color:red;">*</span></label>
                                <input type="text" name="strProductName" class="form-control" value="{{ old('strProductName', $product->strProductName ?? '') }}" placeholder="Enter product name">
                                @if($errors->has('strProductName'))
                                    <span class="text-danger">
                                        {{ $errors->first('strProductName') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">MRP <span style="color:red;">*</span></label>
                                <input type="number" name="MRP" class="form-control" value="{{ old('MRP', $product->MRP ?? 0) }}" placeholder="Enter MRP" min="0">
                                @if($errors->has('MRP'))
                                    <span class="text-danger">
                                        {{ $errors->first('MRP') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Submit' }}</button>
                                <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
