@extends('layouts.app')
 
@section('title', 'Lead Workflow')
 
@section('content')
 <div class="main-content">
     <div class="page-content">
         <div class="container-fluid">
             @include('common.alert')
 
             <div class="row">
                 <div class="col-12">
                     <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                         <div>
                            <h4 class="mb-sm-0">Lead Workflow</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }} | <strong>Current Status:</strong> {{ $lead->iCurrentLeadStatus }}</p>
                         </div>
                        <div class="page-title-right d-flex gap-2">
                            @if($lead->quotation)
                                <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}" class="btn btn-info btn-sm"><i class="fas fa-file-invoice"></i> Quotation</a>
                            @endif
                            @if(in_array(optional(auth()->user()->crmRole)->slug, ['storemanager', 'account']))
                                <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="btn btn-dark btn-sm"><i class="fas fa-money-bill-wave"></i> Payments</a>
                            @endif
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                         </div>
                     </div>
                 </div>
             </div>
 

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Lead Summary</h5></div>
                        <div class="card-body">
                            <p><strong>Customer:</strong> {{ $lead->customer->strCustomer ?? '-' }}</p>
                            <p><strong>Mobile:</strong> {{ $lead->customer->strMobile ?? '-' }}</p>
                            <p><strong>Lead Amount:</strong> {{ number_format((float) $lead->iLeadAmount, 2) }}</p>
                            <p><strong>Measurement Required:</strong> {{ $lead->IsMeasureMentRequired ? 'Yes' : 'No' }}</p>
                            <p><strong>Measurement Date:</strong> {{ $lead->MeasurementVisitDate ?: '-' }}</p>
                            <p><strong>Next Followup:</strong> {{ $lead->NetFollowupdate ?: '-' }}</p>
                            <p class="mb-0"><strong>Fitting Charges:</strong> {{ number_format((float) $lead->iFittingCharges, 2) }}</p>
                         </div>
                    @if(!empty($allowedStatuses))
                        <div class="card">
                            <div class="card-header"><h5 class="mb-0">Add New Status / Discussion</h5></div>
                            <div class="card-body">
                                <form action="{{ route('store.leads.histories.store', $lead->iLeadId) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Next Status <span class="text-danger">*</span></label>
                                        <select name="iStatus" id="iStatus" class="form-control">
                                            <option value="">Select Status</option>
                                            @foreach($allowedStatuses as $status)
                                                <option value="{{ $status }}" {{ old('iStatus') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('iStatus')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Next Follow Up Date</label>
                                        <input type="date" name="NetFolloupwdate" id="NetFolloupwdate" class="form-control" value="{{ old('NetFolloupwdate') }}">
                                        <small class="text-muted">Required for followup-based statuses.</small>
                                        @error('NetFolloupwdate')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Discussion / Comments <span class="text-danger">*</span></label>
                                        <textarea name="strComments" class="form-control" rows="5" placeholder="Enter discussion with customer, visit note, or status update">{{ old('strComments') }}</textarea>
                                        @error('strComments')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Save Discussion</button>
                                </form>
                            </div>
                         </div>
                    @endif
                </div>
 
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Lead Discussion History</h5>
                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn"><i class="fas fa-trash"></i> Bulk Delete</button>
                            @endif
                         </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                <th width="5%"><input type="checkbox" id="selectAll"></th>
                                            @endif
                                            <th>Status</th>
                                            <th>Next Follow Up Date</th>
                                            <th>Comments</th>
                                            <th>Entered By</th>
                                            <th>Entry Date</th>
                                            @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                <th width="8%">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($histories as $history)
                                            <tr>
                                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                    <td><input type="checkbox" class="record-checkbox" value="{{ $history->id }}"></td>
                                                @endif
                                                <td>{{ $history->iStatus }}</td>
                                                <td>{{ $history->NetFolloupwdate ?: '-' }}</td>
                                                <td>{{ $history->strComments }}</td>
                                                <td>{{ $history->user->full_name ?: ($history->user->first_name ?? $history->user->email ?? '-') }}</td>
                                                <td>{{ $history->EntryDate }}</td>
                                                @if(optional(auth()->user()->crmRole)->slug === 'storemanager')
                                                    <td>
                                                        <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $history->id }}" title="Delete"><i class="fas fa-trash"></i></a>
                                                        <form id="delete-form-{{ $history->id }}" action="{{ route('store.leads.histories.delete', [$lead->iLeadId, $history->id]) }}" method="POST" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ optional(auth()->user()->crmRole)->slug === 'storemanager' ? 7 : 5 }}" class="text-center">No history found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">{{ $histories->links() }}</div>
                         </div>
                     </div>
                 </div>
        </div>
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
         if (confirm('Are you sure you want to delete this history?')) {
             $('#delete-form-' + id).submit();
         }
     });
 
     $('#bulkDeleteBtn').on('click', function () {
         let ids = [];
        $('.record-checkbox:checked').each(function () { ids.push($(this).val()); });
         if (ids.length === 0) {
             alert('Please select at least one record.');
             return;
         }

         if (confirm('Are you sure you want to delete selected histories?')) {
             $.ajax({
                 url: "{{ route('store.leads.histories.bulk-delete', $lead->iLeadId) }}",
                 type: "POST",
                data: { _token: "{{ csrf_token() }}", ids: ids },
                 success: function (response) {
                    if (response.status) { location.reload(); }
                 },
                error: function () { alert('Something went wrong.'); }
             });
         }
     });
 });
 </script>
@endsection
