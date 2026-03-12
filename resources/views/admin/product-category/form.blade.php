@extends('layouts.app')

@section('title', isset($category) ? 'Edit Product Category' : 'Add Product Category')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ isset($category) ? 'Edit Product Category' : 'Add Product Category' }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.product-category.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($category) ? route('admin.product-category.update', $category->iCategoryId) : route('admin.product-category.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Category Name <span style="color:red;">*</span></label>
                                <input type="text" name="strCategoryName" class="form-control" value="{{ old('strCategoryName', $category->strCategoryName ?? '') }}" placeholder="Enter category name">
                                @if($errors->has('strCategoryName'))
                                    <span class="text-danger">
                                        {{ $errors->first('strCategoryName') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Submit' }}</button>
                                <a href="{{ route('admin.product-category.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
