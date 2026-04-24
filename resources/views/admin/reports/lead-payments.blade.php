@extends('layouts.app')
@section('title', 'Lead Payments')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Payment Details - {{ $lead->strLeadNo }}</h4>
            <small class="text-muted">Total Received: ₹{{ number_format($paymentTotal, 2) }}</small>
        </div>
        <a href="{{ route('admin.reports.leads.show', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card mb-3"><div class="card-body py-2">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.reports.leads.payments', $lead->iLeadId) }}">
            <div class="col-md-3">
                <label class="form-label mb-1 small">Payment Mode</label>
                <select class="form-select form-select-sm" name="payment_mode">
                    <option value="">All</option>
                    <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ request('payment_mode') === 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success btn-sm">Search</button>
                <a href="{{ route('admin.reports.leads.payments', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive"><table class="table table-bordered mb-0">
        <thead class="table-light"><tr><th>#</th><th>Amount</th><th>Date</th><th>Mode</th><th>Entered By</th></tr></thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payments->firstItem() + $loop->index }}</td>
                    <td>₹{{ number_format((float)$payment->iPaidAmount, 2) }}</td>
                    <td> {{ $payment->PaymentDate ? \Carbon\Carbon::parse($payment->PaymentDate)->format('d-m-Y') : '—' }}
                    </td>
                    <td>{{ ucfirst($payment->PaymentMode ?? '-') }}</td>
                    <td>{{ optional($payment->user)->strUserName ?: optional($payment->user)->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table></div><div class="p-3">{{ $payments->links() }}</div></div>
</div></div></div>
@endsection
