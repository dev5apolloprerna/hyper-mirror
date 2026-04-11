@extends('layouts.app')

@section('title', 'Complaint Master')

@section('content')
@php($roleSlug = optional(auth()->user())->crmRole->slug ?? null)
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row mb-3">
                <div class="col-12 d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Complaint Master</h4>
                </div>
            </div>

            <div class="row">
                @if($roleSlug !== 'account')
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Add Complaint</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('complaints.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->full_name) }}" placeholder="Enter your name">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" placeholder="Enter your email">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('name', auth()->user()->mobile_number) }}" placeholder="Enter your phone">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea  name="address" class="form-control" value="{{ old('address', auth()->user()->address) }}" placeholder="Enter your address">{{ old('comment') }}</textarea>
                                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Comment <span class="text-danger">*</span></label>
                                    <textarea name="comment" class="form-control" rows="4" placeholder="Describe the issue">{{ old('comment') }}</textarea>
                                    @error('comment') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <div class="{{ $roleSlug === 'account' ? 'col-lg-12' : 'col-lg-8' }}">
                    <div class="card">
                        <div class="card-header">
                            <form method="GET" action="{{ route('complaints.index') }}" class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name/email/comment">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success btn-sm">Filter</button>
                                    <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th>Resolved Info</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complaints as $complaint)
                                            <tr>
                                                <td>#{{ $complaint->complain_id }}</td>
                                                <td>
                                                    <div>{{ $complaint->name }}</div>
                                                    <small class="text-muted">{{ $complaint->email }}</small>
                                                </td>
                                                <td>
                                                    <div>{{ $complaint->phone }}</div>
                                                </td><td>
                                                    <div>{{ $complaint->address }}</div>
                                                </td>
                                                <td>{{ $complaint->comment }}</td>
                                                <td>
                                                    @if($complaint->status === 'resolved')
                                                        <span class="badge bg-success">Resolved</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($complaint->status === 'resolved')
                                                        <div><strong>Date:</strong> {{ optional($complaint->resolve_date)->format('d-m-Y H:i') }}</div>
                                                        <div><strong>Payment:</strong> {{ ucfirst((string) $complaint->payment_type) }}</div>
                                                        <div><strong>Amount:</strong> ₹{{ number_format((float) $complaint->amount, 2) }}</div>
                                                        <div><strong>Comment:</strong> {{ $complaint->resolve_comment }}</div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($roleSlug === 'account' && $complaint->status === 'pending')
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary resolve-btn"
                                                            data-url="{{ route('complaints.resolve', $complaint->complain_id) }}"
                                                            data-id="{{ $complaint->complain_id }}">
                                                            Resolve
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No complaints found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $complaints->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($roleSlug === 'account')
<div class="modal fade" id="resolveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="resolveForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resolve Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Resolve Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="resolve_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                        <select name="payment_type" class="form-select" required>
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resolve Comment <span class="text-danger">*</span></label>
                        <textarea name="resolve_comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if($roleSlug === 'account')
<script>
    $(document).ready(function () {
        $('.resolve-btn').on('click', function () {
            const url = $(this).data('url');
            $('#resolveForm').attr('action', url);
            $('#resolveModal').modal('show');
        });
    });
</script>
@endif
@endsection
