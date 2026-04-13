@extends('layouts.app')
@section('title', 'Ledger History')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Ledger History</h4>
                    <p class="text-muted mb-0">View all cash payment ledger entries.</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('store.ledger.index') }}" class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Invoice No</label>
                            <input type="text" name="invoice_no" value="{{ request('invoice_no') }}" class="form-control" placeholder="INV-2026...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">User Type</label>
                            <select name="user_type" class="form-select">
                                <option value="">All</option>
                                @foreach($userTypes as $userType)
                                    <option value="{{ $userType }}" @selected(request('user_type') === $userType)>{{ $userType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('store.ledger.index') }}" class="btn btn-light border">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Entries</h6>
                    <small class="text-muted">{{ $ledgerEntries->total() }} record(s)</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Employee</th>
                                    <th>Open</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Close</th>
                                    <th>Credit Emp ID</th>
                                    <th>Debit Emp ID</th>
                                    <th>User Type</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($ledgerEntries as $entry)
                                <tr>
                                    <td>{{ $ledgerEntries->firstItem() + $loop->index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y h:i A') }}</td>
                                    <td>{{ $entry->strInvoiceNo ?: '—' }}</td>
                                    <td>{{ $entry->emp_user_name ?: ($entry->emp_first_name ?: ('ID: '.$entry->emp_id)) }}</td>
                                    <td>{{ number_format((float)$entry->open, 2) }}</td>
                                    <td>{{ number_format((float)$entry->credit, 2) }}</td>
                                    <td>{{ number_format((float)$entry->debit, 2) }}</td>
                                    <td>{{ number_format((float)$entry->close, 2) }}</td>
                                    <td>{{ $entry->credit_emp_id ?: '—' }}</td>
                                    <td>{{ $entry->debit_emp_id ?: '—' }}</td>
                                    <td>{{ $entry->UserType ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">No ledger entries found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($ledgerEntries->count())
                    <div class="card-footer">{{ $ledgerEntries->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
