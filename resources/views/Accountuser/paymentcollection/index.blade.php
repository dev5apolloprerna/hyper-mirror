@extends('layouts.app')
@section('title', 'Payment Collection')
@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @include('common.alert')

                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">Payment Collection</h4>
                                <p class="text-muted small mb-0">
                                    Manage customer payments and track pending balances.
                                </p>
                            </div>
                            <a href="{{ route('Accountuser.Create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Collection
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Total Collection</h6>
                                <h4>₹ {{ number_format($totalCollection, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Pending Collection</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">₹ {{ number_format($totalPendingCollection, 2) }}</h4>
                                    <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                        data-bs-target="#pendingCollectionModal">
                                        View List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Filters --}}
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <form method="GET" class="row align-items-end g-3">

                            <!-- Search Input -->
                            <div class="col-md-5">
                                <label class="form-label mb-1">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control form-control-sm" placeholder="Search User Name / Mobile">
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-3 d-flex gap-2">

                                <button type="submit" class="btn btn-success btn w-50">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>

                                <a href="{{ route('Accountuser.Accountpayments') }}" class="btn btn-secondary btn-sm w-50">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>

                            </div>

                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>User</th>
                                        <th>Mobile</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Date</th>
                                        <th>Comment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($payments as $key => $row)
                                        <tr>
                                            <td>{{ $payments->firstItem() + $key }}</td>
                                            <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                                            <td>{{ $row->mobile_number }}</td>
                                            <td>₹ {{ number_format($row->amount, 2) }}</td>

                                            <td>
                                                @if ($row->payment_mode == 0)
                                                    <span class="badge bg-primary">Cash</span>
                                                @else
                                                    <span class="badge bg-info">Online</span>
                                                @endif
                                            </td>

                                            <td>{{ date('d-m-Y', strtotime($row->payment_date)) }}</td>

                                            <td>
                                                @if ($row->comment)
                                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                        data-bs-target="#commentModal"
                                                        onclick="showComment(`{{ addslashes($row->comment) }}`)">
                                                        View
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $row->account_payment_id }}, {{ $row->emp_id }})">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No Data Found!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="modal fade" id="commentModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Payment Notes</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p id="commentText" class="mb-0"></p>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-2">
                            {{ $payments->links() }}
                        </div>

                    </div>
                </div>
                {{-- new --}}
                <div class="modal fade" id="pendingCollectionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pending Collection List</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>User</th>
                                                <th>Mobile</th>
                                                <th class="text-end">Pending Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pendingCollections as $index => $pendingUser)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ trim(($pendingUser->first_name ?? '') . ' ' . ($pendingUser->last_name ?? '')) }}
                                                    </td>
                                                    <td>{{ $pendingUser->mobile_number ?? '-' }}</td>
                                                    <td class="text-end">₹
                                                        {{ number_format((float) $pendingUser->pending_amount, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No pending collection
                                                        found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($pendingCollections->isNotEmpty())
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="3" class="text-end">Total Pending</th>
                                                    <th class="text-end">₹ {{ number_format($totalPendingCollection, 2) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- new --}}

            </div>
        </div>
    </div>
@endsection
<script>
    function showComment(comment) {
        document.getElementById('commentText').innerText = comment;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(id, emp_id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This payment will be deleted and ledger will be reversed!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href =
                    "{{ url('Accountuser/payment-delete') }}/" + id + "/" + emp_id;
            }
        });
    }
</script>
