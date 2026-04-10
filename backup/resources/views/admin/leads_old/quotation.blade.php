@extends('layouts.app')
@section('title','Generate Quotation')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
<h4>Generate Quotation - {{ $lead->strLeadNo }}</h4>
<form method="POST" action="{{ route('store.leads.quotation.store', $lead) }}" class="row g-2">@csrf
<div class="col-md-3"><select name="iProductCategoryId" class="form-control" required>@foreach($categories as $cat)<option value="{{ $cat->iCategoryId }}">{{ $cat->strCategoryName }}</option>@endforeach</select></div>
<div class="col-md-3"><select name="iProductId" class="form-control" required>@foreach($products as $p)<option value="{{ $p->iProductId }}">{{ $p->strProductName }}</option>@endforeach</select></div>
<div class="col-md-2"><input name="decHeight" type="number" step="0.01" class="form-control" placeholder="Height" required></div>
<div class="col-md-2"><input name="decWidth" type="number" step="0.01" class="form-control" placeholder="Width" required></div>
<div class="col-md-2"><input name="decRatePerSqft" type="number" step="0.01" class="form-control" placeholder="Rate/Sqft" required></div>
<div class="col-md-12"><button class="btn btn-primary">Save Quotation</button></div>
</form>
</div></div></div>
@endsection
