@extends('layouts.app')

@section('title', 'Lead Payments')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            @php
                $totalReceived = (float) $payments->sum('iPaidAmount');
                $leadAmount = (float) ($lead->iLeadAmount ?? 0);
                $pendingAmount = max(0, $leadAmount - $totalReceived);
            @endphp

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-muted">Lead Amount</small>
                            <h5 class="mb-0">₹{{ number_format($leadAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-muted">Total Received</small>
                            <h5 class="mb-0 text-success">₹{{ number_format($totalReceived, 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <small class="text-muted">Pending Amount</small>
                            <h5 class="mb-0 {{ $pendingAmount > 0 ? 'text-warning' : 'text-success' }}">₹{{ number_format($pendingAmount, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $canManagePayments ? 'Add Payment Entry' : 'Payment Access' }}</h5>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="card-body">
                            @if($canManagePayments)
                                <form action="{{ route('store.leads.payments.store', $lead->iLeadId) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="iPaidAmount" class="form-control" value="{{ old('iPaidAmount') }}" placeholder="Enter paid amount" required>
                                        @error('iPaidAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="PaymentDate" class="form-control" value="{{ old('PaymentDate') }}" required>
                                        @error('PaymentDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                        <input type="text" name="PaymentMode" class="form-control" value="{{ old('PaymentMode') }}" placeholder="Cash / UPI / Bank" required>
                                        @error('PaymentMode') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Back</a>
                                </form>
                            @else
                                <div class="alert alert-info mb-3">
                                    Sales/store users can view received payment details.
                                    Payment entries are managed by accountant login only.
                                </div>
                                <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Back</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Payment Listing</h5>
                                @if($canManagePayments)
                                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                        <i class="fas fa-trash"></i> Bulk Delete
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="row g-2 mb-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Search Payment</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Amount / date / mode / user">
                                </div>
                                <div class="col-md-6 d-flex gap-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-search"></i> Search</button>
                                    <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            @if($canManagePayments)
                                                <th width="5%"><input type="checkbox" id="selectAll"></th>
                                            @endif
                                            <th>Paid Amount</th>
                                            <th>Payment Date</th>
                                            <th>Payment Mode</th>
                                            <th>Entered By</th>
                                            @if($canManagePayments)
                                                <th width="12%">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            <tr>
                                                @if($canManagePayments)
                                                    <td><input type="checkbox" class="record-checkbox" value="{{ $payment->iLeadPaymentId }}"></td>
                                                @endif
                                                <td>₹{{ number_format((float)$payment->iPaidAmount, 2) }}</td>
                                                <td>{{ $payment->PaymentDate }}</td>
                                                <td>{{ $payment->PaymentMode }}</td>
                                                 <td>
                                                    {{ $payment->user->full_name
                                                        ?: ($payment->user->name
                                                            ?? ($payment->user->strUserName ?? '—')) }}
                                                </td>
                                                @if($canManagePayments)
                                                    <td>
                                                        <a href="javascript:void(0);"
                                                           class="text-primary me-2 edit-payment-btn"
                                                           title="Edit"
                                                           data-id="{{ $payment->iLeadPaymentId }}"
                                                           data-amount="{{ $payment->iPaidAmount }}"
                                                           data-date="{{ $payment->PaymentDate }}"
                                                           data-mode="{{ $payment->PaymentMode }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $payment->iLeadPaymentId }}" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>

                                                        <form id="delete-form-{{ $payment->iLeadPaymentId }}" action="{{ route('store.leads.payments.delete', [$lead->iLeadId, $payment->iLeadPaymentId]) }}" method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $canManagePayments ? 6 : 4 }}" class="text-center">No payments found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($canManagePayments)
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editPaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaymentModalLabel">Edit Lead Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="iPaidAmount" id="edit_iPaidAmount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="PaymentDate" id="edit_PaymentDate" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <input type="text" name="PaymentMode" id="edit_PaymentMode" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if($canManagePayments)
<script>
$(document).ready(function () {
    $('#selectAll').on('click', function () {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.delete-record').on('click', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this payment?')) {
            $('#delete-form-' + id).submit();
        }
    });

    $('#bulkDeleteBtn').on('click', function () {
        let ids = [];
        $('.record-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete selected payments?')) {
            $.ajax({
                url: "{{ route('store.leads.payments.bulk-delete', $lead->iLeadId) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message || 'Something went wrong.');
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    });

    $('.edit-payment-btn').on('click', function () {
        const id = $(this).data('id');
        const amount = $(this).data('amount');
        const date = $(this).data('date');
        const mode = $(this).data('mode');

        $('#edit_iPaidAmount').val(amount);
        $('#edit_PaymentDate').val(date);
        $('#edit_PaymentMode').val(mode);
        $('#editPaymentForm').attr('action', "{{ url('store-manager/leads/' . $lead->iLeadId . '/payments') }}/" + id + "/update");

        $('#editPaymentModal').modal('show');
    });
});
</script>
@endif
@endsection