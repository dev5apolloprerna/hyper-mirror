@extends('layouts.app')
@section('title', 'Add Payment Collection')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @include('common.alert')

                <!-- Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Add Payment Collection</h4>
                            <div class="page-title-right">
                                <a href="{{ route('Accountuser.Accountpayments') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('Accountuser.Store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- User Dropdown -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Select User <span style="color:red;">*</span></label>
                                    <select name="user_id" class="form-control" required>
                                        <option value="">Select User</option>
                                        @foreach ($getuser as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name ?? '' }}
                                                ({{ $user->mobile_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                    @endif
                                </div>
                                <!-- Amount -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Amount <span style="color:red;">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control"
                                        value="{{ old('amount') }}" placeholder="0.00" required>
                                    @if ($errors->has('amount'))
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    @endif
                                </div>
                                <!-- Payment Mode -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Payment Mode <span style="color:red;">*</span></label>
                                    <select name="payment_mode" class="form-control" required>
                                        <option value="">Select Mode</option>
                                        <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="Online" {{ old('payment_mode') == 'Online' ? 'selected' : '' }}>
                                            Online</option>

                                    </select>
                                    @if ($errors->has('payment_mode'))
                                        <span class="text-danger">{{ $errors->first('payment_mode') }}</span>
                                    @endif
                                </div>
                                <!-- Notes -->
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Enter notes">{{ old('notes') }}</textarea>
                                    @if ($errors->has('notes'))
                                        <span class="text-danger">{{ $errors->first('notes') }}</span>
                                    @endif
                                </div>
                                <!-- Buttons -->
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('Accountuser.Accountpayments') }}"
                                        class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
