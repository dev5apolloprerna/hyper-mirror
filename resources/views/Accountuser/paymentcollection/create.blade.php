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
                        <form action="{{ route('Accountuser.Store') }}" method="POST" id="paymentCollectionForm">
                            @csrf
                            <div class="row">
                                <!-- User Dropdown -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Select User <span style="color:red;">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control" required>
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
                                    <small id="availableAmountText" class="text-primary d-block mt-2">
                                        Available Collection Amount: ₹0.00
                                    </small>
                                </div>
                                <!-- Amount -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Amount <span style="color:red;">*</span></label>
                                    <input type="number" step="0.01" min="1" name="amount" id="amount"
                                        class="form-control" value="{{ old('amount') }}" placeholder="0.00" required>
                                    <small id="amountValidationError" class="text-danger d-none"></small>
                                    @if ($errors->has('amount'))
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    @endif
                                </div>
                                <!-- Payment Mode -->
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Payment Mode <span style="color:red;">*</span></label>
                                    <select name="payment_mode" class="form-control" required>
                                        <option value="">Select Mode</option>
                                        <option value="Cash"
                                            {{ old('payment_mode', 'Cash') == 'Cash' ? 'selected' : '' }}>
                                            Cash
                                        </option>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id');
        const amountInput = document.getElementById('amount');
        const paymentCollectionForm = document.getElementById('paymentCollectionForm');
        const availableAmountText = document.getElementById('availableAmountText');
        const amountValidationError = document.getElementById('amountValidationError');
        let availableAmount = 0;

        const resetAmountDisplay = () => {
            availableAmount = 0;
            amountInput.removeAttribute('max');
            availableAmountText.innerText = 'Available Collection Amount: ₹0.00';
            amountValidationError.classList.add('d-none');
            amountValidationError.innerText = '';
        };

        const validateAmount = () => {
            const enteredAmount = parseFloat(amountInput.value || 0);
            if (enteredAmount > availableAmount && availableAmount >= 0) {
                amountValidationError.innerText =
                    `Amount cannot be greater than available collection amount (₹${availableAmount.toFixed(2)}).`;
                amountValidationError.classList.remove('d-none');
                return;
            }

            amountValidationError.classList.add('d-none');
            amountValidationError.innerText = '';
        };

        userSelect.addEventListener('change', async function() {
            const userId = this.value;
            amountInput.value = '';

            if (!userId) {
                resetAmountDisplay();
                return;
            }

            try {
                const response = await fetch(
                    `{{ url('Accountuser/available-amount') }}/${userId}`);
                const data = await response.json();
                availableAmount = parseFloat(data.available_amount || 0);
                amountInput.setAttribute('max', availableAmount.toFixed(2));
                availableAmountText.innerText =
                    `Available Collection Amount: ₹${availableAmount.toFixed(2)}`;
                validateAmount();
            } catch (error) {
                resetAmountDisplay();
            }
        });

        amountInput.addEventListener('input', validateAmount);

        paymentCollectionForm.addEventListener('submit', function(e) {
            const enteredAmount = parseFloat(amountInput.value || 0);
            if (enteredAmount > availableAmount) {
                e.preventDefault();
                validateAmount();
            }
        });
    });
</script>
