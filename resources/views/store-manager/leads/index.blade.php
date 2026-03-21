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
                        <div>
                            <h4 class="mb-sm-0">{{ $roleSlug === 'storemanager' ? 'Lead List' : ucfirst($roleSlug) . ' Lead Queue' }}</h4>
                            <p class="mb-0 mt-1 text-muted">Manage followup, discussions, and role-based status movement.</p>
                        </div>
                         <div class="page-title-right">
                            @if($roleSlug === 'storemanager')
                                <a href="{{ route('store.leads.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Lead
                                </a>
                            @endif
                         </div>
                     </div>
                 </div>
             </div>
 
             <div class="card">
                 <div class="card-body">
                    <form method="GET" action="{{ route('store.leads.index') }}" class="row mb-3 g-3">
                         <div class="col-md-4">
                             <label class="form-label">Search Lead</label>
                             <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Lead no / customer / mobile / status">
                         </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Followup Bucket</label>
                            <select name="followup" class="form-control">
                                <option value="">All</option>
                                <option value="today" {{ request('followup') === 'today' ? 'selected' : '' }}>Today's Followup</option>
                                <option value="overdue" {{ request('followup') === 'overdue' ? 'selected' : '' }}>Over Due Followup</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-success btn-sm w-100"><i class="fas fa-search"></i> Search</button>
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                                    <th width="18%">Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @forelse($leads as $lead)
                                     <tr>
                                         <td>{{ $lead->strLeadNo }}</td>
                                         <td>{{ $lead->customer->strCustomer ?? '' }}</td>
                                         <td>{{ $lead->customer->strMobile ?? '' }}</td>
                                        <td><span class="badge bg-info-subtle text-dark">{{ $lead->iCurrentLeadStatus }}</span></td>
                                        <td>{{ number_format((float) $lead->iLeadAmount, 2) }}</td>
                                        <td>{{ $lead->NetFollowupdate ?: '-' }}</td>
                                        <td>{{ $lead->IsMeasureMentRequired == 1 ? 'Yes' : 'No' }}</td>
                                         <td>
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <a href="{{ route('store.leads.histories.index', $lead->iLeadId) }}" class="btn btn-sm btn-primary" title="Open workflow">
                                                    <i class="fas fa-plus"></i>
                                                </a>

                                                @if($lead->quotation)
                                                    <a href="{{ route('store.leads.quotation-view', $lead->iLeadId) }}" class="text-secondary" title="View Quotation">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif

                                                @if($roleSlug === 'storemanager')
                                                    <a href="{{ route('store.leads.quotation', $lead->iLeadId) }}" class="text-success" title="Manage Quotation">
                                                        <i class="fas fa-file-signature"></i>
                                                    </a>
                                                    <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="text-warning" title="Lead Designs">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                @endif

                                                @if(in_array($roleSlug, ['storemanager', 'account']))
                                                    <a href="{{ route('store.leads.payments.index', $lead->iLeadId) }}" class="text-dark" title="Lead Payments">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @endif
                                            </div>
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
 
                    <div class="mt-3 d-flex justify-content-center">{{ $leads->links() }}</div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 @endsection
