@extends('layouts.app')
@section('title','Create Lead')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
<h4>Create Lead</h4>
<form method="POST" action="{{ route('store.leads.store') }}" class="row g-3">@csrf
<div class="col-md-3"><input id="mobile" name="strMobile" class="form-control" placeholder="Customer Mobile" required></div>
<div class="col-md-4"><input id="customer" name="strCustomer" class="form-control" placeholder="Customer Name" required></div>
<div class="col-md-5"><input name="strAddress" class="form-control" placeholder="Customer Address"></div>
<div class="col-md-6"><input name="SiteAddress" class="form-control" placeholder="Site Address"></div>
<div class="col-md-3"><select name="IsMeasureMentRequired" class="form-control"><option value="1">Measurement Required</option><option value="0">No Measurement</option></select></div>
<div class="col-md-3"><input type="date" name="MeasurementVisitDate" class="form-control"></div>
<div class="col-md-3"><input type="date" name="quotation_date" class="form-control" placeholder="Quotation Date"></div>
<div class="col-md-12"><button class="btn btn-primary">Save Lead</button></div>
</form>
</div></div></div>
<script>
document.getElementById('mobile').addEventListener('blur', function(){
 fetch(`{{ route('store.customer.check') }}?mobile=${this.value}`).then(r=>r.json()).then(data=>{
  if(data){document.getElementById('customer').value=data.strCustomer || '';}
 });
});
</script>
@endsection
