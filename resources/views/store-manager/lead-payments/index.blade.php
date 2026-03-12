@extends('layouts.app')

@section('title', 'Lead Payments')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-md-4">
                    {{-- Add Form --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Lead Payment</h5>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('store.leads.payments.store', $lead->iLeadId) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Paid Amount <span style="color:red;">*</span></label>
                                    <input type="number" step="0.01" min="0.01" name="iPaidAmount" class="form-control" value="{{ old('iPaidAmount') }}" placeholder="Enter paid amount">
                                    @if($errors->has('iPaidAmount'))
                                        <span class="text-danger">
                                            {{ $errors->first('iPaidAmount') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Date <span style="color:red;">*</span></label>
                                    <input type="date" name="PaymentDate" class="form-control" value="{{ old('PaymentDate') }}">
                                    @if($errors->has('PaymentDate'))
                                        <span class="text-danger">
                                            {{ $errors->first('PaymentDate') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Mode <span style="color:red;">*</span></label>
                                    <input type="text" name="PaymentMode" class="form-control" value="{{ old('PaymentMode') }}" placeholder="Enter payment mode">
                                    @if($errors->has('PaymentMode'))
                                        <span class="text-danger">
                                            {{ $errors->first('PaymentMode') }}
                                        </span>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('store.leads.index') }}" class="btn btn-secondary">Back</a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    {{-- Listing --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Payment Listing</h5>
                                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                    <i class="fas fa-trash"></i> Bulk Delete
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="row mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Search Payment</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Amount / date / mode / user">
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-success btn-sm mt-2">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="btn btn-secondary btn-sm mt-2">Reset</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>Paid Amount</th>
                                            <th>Payment Date</th>
                                            <th>Payment Mode</th>
                                            <th>User</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $payment->iLeadPaymentId }}">
                                                </td>
                                                <td>{{ $payment->iPaidAmount }}</td>
                                                <td>{{ $payment->PaymentDate }}</td>
                                                <td>{{ $payment->PaymentMode }}</td>
                                                <td>{{ $payment->user->name ?? '' }}</td>
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
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No payments found.</td>
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

{{-- Edit Modal --}}
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
                        <label class="form-label">Paid Amount <span style="color:red;">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="iPaidAmount" id="edit_iPaidAmount" class="form-control" placeholder="Enter paid amount">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span style="color:red;">*</span></label>
                        <input type="date" name="PaymentDate" id="edit_PaymentDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Mode <span style="color:red;">*</span></label>
                        <input type="text" name="PaymentMode" id="edit_PaymentMode" class="form-control" placeholder="Enter payment mode">
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
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('#selectAll').on('click', function () {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.delete-record').on('click', function () {
        let id = $(this).data('id');

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
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    });

    $('.edit-payment-btn').on('click', function () {
        let id = $(this).data('id');
        let amount = $(this).data('amount');
        let date = $(this).data('date');
        let mode = $(this).data('mode');

        $('#edit_iPaidAmount').val(amount);
        $('#edit_PaymentDate').val(date);
        $('#edit_PaymentMode').val(mode);
        $('#editPaymentForm').attr('action', "{{ url('store-manager/leads/' . $lead->iLeadId . '/payments') }}/" + id + "/update");

        $('#editPaymentModal').modal('show');
    });
});
</script>
@endsection 