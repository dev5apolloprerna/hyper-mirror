@extends('layouts.app')
@section('title','Lead Listing')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
<div class="d-flex justify-content-between"><h4>Lead Listing</h4><a href="{{ route('store.leads.create') }}" class="btn btn-primary">New Lead</a></div>
<table class="table table-bordered mt-3"><tr><th>Lead No</th><th>Customer</th><th>Mobile</th><th>Status</th><th>Next Followup</th><th>Action</th></tr>
@foreach($leads as $lead)
<tr>
<td>{{ $lead->strLeadNo }}</td><td>{{ $lead->customer->strCustomer ?? '-' }}</td><td>{{ $lead->customer->strMobile ?? '-' }}</td>
<td>{{ $lead->iCurrentLeadStatus }}</td><td>{{ $lead->NetFollowupdate }}</td>
<td>
@if($lead->iCurrentLeadStatus === 'Quotation Pending')
<a class="btn btn-sm btn-info" href="{{ route('store.leads.quotation',$lead) }}">Generate Quotation</a>
@endif
<form action="{{ route('store.leads.status',$lead) }}" method="POST" class="d-flex gap-2 mt-1">@csrf
<input name="iStatus" class="form-control form-control-sm" placeholder="New Status" required>
<input type="date" name="NetFolloupwdate" class="form-control form-control-sm">
<button class="btn btn-sm btn-secondary">+</button>
</form>
</td>
</tr>
@endforeach
</table>{{ $leads->links() }}
</div></div></div>
@endsection
