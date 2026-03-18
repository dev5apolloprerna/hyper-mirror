@extends('layouts.app')

@section('title', 'Lead List')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Lead List</h4>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Lead
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <form method="GET" action="{{ route('store.leads.index') }}" class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Search Lead</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Lead no / customer / mobile / status">
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="submit" class="btn btn-success btn-sm mt-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm mt-2">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Lead No</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Lead Amount</th>
                                    <th>Next Follow Up</th>
                                    <th>Measurement Required</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                    <tr>
                                        <td>{{ $lead->strLeadNo }}</td>
                                        <td>{{ $lead->customer->strCustomer ?? '' }}</td>
                                        <td>{{ $lead->customer->strMobile ?? '' }}</td>
                                        <td>{{ $lead->iCurrentLeadStatus }}</td>
                                        <td>{{ $lead->iLeadAmount }}</td>
                                        <td>{{ $lead->NetFollowupdate }}</td>
                                        <td>
                                            @if($lead->IsMeasureMentRequired == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td>
                                           
                                            <a href="{{ route('store.leads.quotation', $lead->iLeadId) }}" class="text-secondary me-2" title="View Quotation">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>

                                            <a href="javascript:void(0);" 
                                               class="text-success update-status-btn"
                                               data-id="{{ $lead->iLeadId }}"
                                               data-status="{{ $lead->iCurrentLeadStatus }}"
                                               data-followup="{{ $lead->NetFollowupdate }}"
                                               title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="text-warning me-2" title="Lead Designs">
                                                <i class="fas fa-images"></i>
                                            </a>
                                            <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}" class="text-info me-2" title="Lead History">
                                                <i class="fas fa-history"></i>
                                            </a>

                                            <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="text-dark me-2" title="Lead Payments">
                                                <i class="fas fa-inr"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $leads->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="statusForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Lead Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span style="color:red;">*</span></label>
                        <input type="text" name="iStatus" id="iStatus" class="form-control" placeholder="Enter status">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Next Follow Up Date</label>
                        <input type="date" name="NetFollowupdate" id="NetFollowupdate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comments</label>
                        <textarea name="strComments" class="form-control" rows="3" placeholder="Enter comments"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('.update-status-btn').on('click', function () {
            let leadId = $(this).data('id');
            let status = $(this).data('status');
            let followup = $(this).data('followup');

            $('#iStatus').val(status);
            $('#NetFollowupdate').val(followup);
            $('#statusForm').attr('action', "{{ url('store-manager/leads') }}/" + leadId + "/update-status");

            $('#statusModal').modal('show');
        });
    });
</script>
@endsection
