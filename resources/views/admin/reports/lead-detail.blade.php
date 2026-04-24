@extends('layouts.app')
@section('title', 'Lead Detail')

@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lead Detail - {{ $lead->strLeadNo }}</h4>
        <a href="{{ route('admin.reports.leads') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Customer:</strong><br>{{ optional($lead->customer)->strCustomer ?? '—' }}</div>
            <div class="col-md-3"><strong>Mobile:</strong><br>{{ optional($lead->customer)->strMobile ?? '—' }}</div>
            <div class="col-md-3"><strong>Showroom:</strong><br>{{ optional($lead->showroom)->strShowRoomName ?? '—' }}</div>
            <div class="col-md-3"><strong>Status:</strong><br>{{ $lead->iCurrentLeadStatus }}</div>
            <div class="col-md-3"><strong>Lead Amount:</strong><br>₹{{ number_format((float)($lead->iLeadAmount ?? 0), 2) }}</div>
            <div class="col-md-3"><strong>Created By:</strong><br>{{ optional($lead->createdBy)->strUserName ?: optional($lead->createdBy)->name ?? '—' }}</div>
            <div class="col-md-3">    <strong>Created Date:</strong><br>    {{ $lead->CreatedDate ? \Carbon\Carbon::parse($lead->CreatedDate)->format('d-m-Y') : '—' }}</div><div class="col-md-3">    <strong>Next Follow-up:</strong><br>    {{ $lead->NetFollowupdate ? \Carbon\Carbon::parse($lead->NetFollowupdate)->format('d-m-Y') : '—' }}</div>
        </div>
    </div></div>

    <div class="row g-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">History Entries</div><h4>{{ $historyCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Quotation Entries</div><h4>{{ $quotationCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Payment Entries</div><h4>{{ $paymentCount }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center"><div class="text-muted small">Payment Received</div><h4>₹{{ number_format($paymentTotal, 2) }}</h4></div></div></div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('admin.reports.leads.histories', $lead->iLeadId) }}" class="btn btn-outline-info btn-sm">Lead History</a>
        <a href="{{ route('admin.reports.leads.quotations', $lead->iLeadId) }}" class="btn btn-outline-secondary btn-sm">Quotation History</a>
        <a href="{{ route('admin.reports.leads.payments', $lead->iLeadId) }}" class="btn btn-outline-dark btn-sm">Payment Details</a>
    </div>
</div></div></div>
@endsection
