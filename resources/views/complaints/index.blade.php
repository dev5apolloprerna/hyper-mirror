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
                    @if ($roleSlug !== 'account')
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
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', auth()->user()->full_name) }}"
                                                placeholder="Enter your name">
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Invoice No (Optional)</label>
                                            <input type="text" name="invoice_no" class="form-control"
                                                value="{{ old('invoice_no') }}"
                                                placeholder="Enter quotation no">
                                            @error('invoice_no')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Showroom</label>
                                            <select name="iShowroomId" class="form-select">
                                                <option value="">Select showroom</option>
                                                @foreach(($showrooms ?? collect()) as $showroom)
                                                    <option value="{{ $showroom->iShowroomId }}" @selected(old('iShowroomId') == $showroom->iShowroomId)>{{ $showroom->strShowRoomName }}</option>
                                                @endforeach
                                            </select>
                                            @error('iShowroomId')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mobile</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone') }}"
                                                placeholder="Enter your phone">
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" class="form-control" value="{{ old('address', auth()->user()->address) }}"
                                                placeholder="Enter your address">{{ old('comment') }}</textarea>
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Comment <span class="text-danger">*</span></label>
                                            <textarea name="comment" class="form-control" rows="4" placeholder="Describe the issue">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="{{ $roleSlug === 'fitting' ? 'col-lg-8' : 'col-lg-8' }}">
                        <div class="card">
                            <div class="card-header">
                                <form method="GET" action="{{ route('complaints.index') }}" class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="search" class="form-control"
                                            value="{{ request('search') }}" placeholder="Search by name/email/comment">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="pending"
                                                {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="resolved"
                                                {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="iShowroomId" class="form-select">
                                            <option value="">All Showrooms</option>
                                            @foreach(($showrooms ?? collect()) as $showroom)
                                                <option value="{{ $showroom->iShowroomId }}" @selected((string)request('iShowroomId') === (string)$showroom->iShowroomId)>{{ $showroom->strShowRoomName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">

                                        <button type="submit" class="btn btn-success btn-sm">Filter</button>
                                        <a href="{{ route('complaints.index') }}"
                                            class="btn btn-secondary btn-sm">Reset</a>
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
                                                <th>Invoice No</th>
                                                <th>Showroom</th>
                                                <th>Comment</th>
                                                <th>Status</th>
                                                <th>Resolved Info</th>
                                                @if ($roleSlug == 'fitting')
                                                <th width="10%">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($complaints as $complaint)
                                                <tr>
                                                    <td>{{ $complaint->complain_id }}</td>
                                                    <td>
                                                        <div>{{ $complaint->name }}</div>
                                                        <small class="text-muted">{{ $complaint->email }}</small>
                                                    </td>
                                                    <td>
                                                        <div>{{ $complaint->phone }}</div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $complaint->address }}</div>
                                                    </td>
                                                     <td>
                                                        <div>{{ $complaint->invoice_no ?: ($complaint->quotation_no ?: '—') }}</div>
                                                    </td>
                                                    <td>{{ optional($complaint->showroom)->strShowRoomName ?? '—' }}</td>
                                                    <td>{{ $complaint->comment }}</td>
                                                    <td>
                                                        @if ($complaint->status === 'resolved')
                                                            <span class="badge bg-success">Resolved</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($complaint->status === 'resolved')
                                                            <div><strong>Date:</strong>
                                                                {{ optional($complaint->resolve_date)->format('d-m-Y H:i') }}
                                                            </div>
                                                            <div><strong>Payment:</strong>
                                                                {{ ucfirst((string) $complaint->payment_type) }}</div>
                                                            <div><strong>Amount:</strong>
                                                                ₹{{ number_format((float) $complaint->amount, 2) }}</div>
                                                            <div><strong>Comment:</strong>
                                                                {{ $complaint->resolve_comment }}</div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    @if ($roleSlug == 'fitting')
                                                    <td>
                                                        @if ($roleSlug === 'fitting' && $complaint->status === 'pending')
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
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No complaints found.</td>
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

    @if ($roleSlug === 'fitting')
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
                                <input type="date" name="resolve_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                    <select name="payment_type" id="payment_type" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="warranty">Warranty</option>
                                </select>
                            </div>
                            <div class="mb-3" id="amount_wrapper">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control" min="0" step="0.01"
                                    required>
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
    @if ($roleSlug === 'fitting')
        <script>
            $(document).ready(function() {
                function toggleAmountField() { 
                    const isWarranty = $('#payment_type').val() === 'warranty';
                    const amountInput = $('#amount');
                    const amountWrapper = $('#amount_wrapper');

                    amountInput.prop('required', !isWarranty);
                    if (isWarranty) {
                        amountInput.val('');
                        amountWrapper.hide();
                    } else {
                        amountWrapper.show();
                    }
                }


                $('.resolve-btn').on('click', function() {
                    const url = $(this).data('url');
                    $('#resolveForm').attr('action', url);
                    $('#resolveForm')[0].reset();
                    toggleAmountField();
                    $('#resolveModal').modal('show');
                });
                $('#payment_type').on('change', toggleAmountField);
            });
        </script>
    @endif
@endsection
