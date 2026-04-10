@extends('layouts.app')

@section('title', 'Customer')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Customer</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Side Add Form --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Customer</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.customer.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Customer Name <span style="color:red;">*</span></label>
                                    <input type="text" name="strCustomer" class="form-control" value="{{ old('strCustomer') }}" placeholder="Enter customer name">
                                    @if($errors->has('strCustomer'))
                                        <span class="text-danger">
                                            {{ $errors->first('strCustomer') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mobile <span style="color:red;">*</span></label>
                                    <input type="text" name="strMobile" class="form-control" value="{{ old('strMobile') }}" placeholder="Enter mobile number" maxlength="10">
                                    @if($errors->has('strMobile'))
                                        <span class="text-danger">
                                            {{ $errors->first('strMobile') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="strAddress" class="form-control" rows="4" placeholder="Enter address">{{ old('strAddress') }}</textarea>
                                    @if($errors->has('strAddress'))
                                        <span class="text-danger">
                                            {{ $errors->first('strAddress') }}
                                        </span>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right Side Listing --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                        <i class="fas fa-trash"></i> Bulk Delete
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('admin.customer.index') }}" class="d-flex justify-content-end">
                                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search Customer" style="max-width:250px;">
                                        <button type="submit" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>Customer Name</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customers as $customer)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $customer->iCustomerId }}">
                                                </td>
                                                <td>{{ $customer->strCustomer }}</td>
                                                <td>{{ $customer->strMobile }}</td>
                                                <td>{{ $customer->strAddress }}</td>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="text-primary me-2 edit-customer-btn"
                                                       title="Edit"
                                                       data-id="{{ $customer->iCustomerId }}"
                                                       data-name="{{ $customer->strCustomer }}"
                                                       data-mobile="{{ $customer->strMobile }}"
                                                       data-address="{{ $customer->strAddress }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);"
                                                       class="text-danger delete-record"
                                                       data-id="{{ $customer->iCustomerId }}"
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $customer->iCustomerId }}" action="{{ route('admin.customer.delete', $customer->iCustomerId) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $customers->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editCustomerForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Name <span style="color:red;">*</span></label>
                        <input type="text" name="strCustomer" id="edit_strCustomer" class="form-control" placeholder="Enter customer name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile <span style="color:red;">*</span></label>
                        <input type="text" name="strMobile" id="edit_strMobile" class="form-control" placeholder="Enter mobile number" maxlength="10">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="strAddress" id="edit_strAddress" class="form-control" rows="4" placeholder="Enter address"></textarea>
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

        if (confirm('Are you sure you want to delete this record?')) {
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

        if (confirm('Are you sure you want to delete selected records?')) {
            $.ajax({
                url: "{{ route('admin.customer.bulkDelete') }}",
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

    $('.edit-customer-btn').on('click', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mobile = $(this).data('mobile');
        let address = $(this).data('address');

        $('#edit_strCustomer').val(name);
        $('#edit_strMobile').val(mobile);
        $('#edit_strAddress').val(address);
        $('#editCustomerForm').attr('action', "{{ url('admin/customer/update') }}/" + id);

        $('#editCustomerModal').modal('show');
    });
});
</script>
@endsection