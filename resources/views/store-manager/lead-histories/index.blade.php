@extends('layouts.app')

@section('title', 'Lead History List')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Lead History</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add Form --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add Lead History</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.leads.histories.store', $lead->iLeadId) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Status <span style="color:red;">*</span></label>
                                <input type="text" name="iStatus" class="form-control" value="{{ old('iStatus') }}" placeholder="Enter status">
                                @if($errors->has('iStatus'))
                                    <span class="text-danger">
                                        {{ $errors->first('iStatus') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">Next Follow Up Date</label>
                                <input type="date" name="NetFolloupwdate" class="form-control" value="{{ old('NetFolloupwdate') }}">
                                @if($errors->has('NetFolloupwdate'))
                                    <span class="text-danger">
                                        {{ $errors->first('NetFolloupwdate') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-4 mb-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Comments</label>
                                <textarea name="strComments" class="form-control" rows="4" placeholder="Enter comments">{{ old('strComments') }}</textarea>
                                @if($errors->has('strComments'))
                                    <span class="text-danger">
                                        {{ $errors->first('strComments') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listing --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">History Listing</h5>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                        <i class="fas fa-trash"></i> Bulk Delete
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Status</th>
                                    <th>Next Follow Up Date</th>
                                    <th>Comments</th>
                                    <th>Entered By</th>
                                    <th>Entry Date</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="record-checkbox" value="{{ $history->id }}">
                                        </td>
                                        <td>{{ $history->iStatus }}</td>
                                        <td>{{ $history->NetFolloupwdate }}</td>
                                        <td>{{ $history->strComments }}</td>
                                        <td>{{ $history->user->name ?? '' }}</td>
                                        <td>{{ $history->EntryDate }}</td>
                                        <td>
                                            <a href="javascript:void(0);" 
                                               class="text-primary me-2 edit-history-btn"
                                               title="Edit"
                                               data-id="{{ $history->id }}"
                                               data-status="{{ $history->iStatus }}"
                                               data-followup="{{ $history->NetFolloupwdate }}"
                                               data-comments="{{ $history->strComments }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $history->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>

                                            <form id="delete-form-{{ $history->id }}" action="{{ route('store.leads.histories.delete', [$lead->iLeadId, $history->id]) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editHistoryModal" tabindex="-1" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editHistoryForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHistoryModalLabel">Edit Lead History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Status <span style="color:red;">*</span></label>
                            <input type="text" name="iStatus" id="edit_iStatus" class="form-control" placeholder="Enter status">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Next Follow Up Date</label>
                            <input type="date" name="NetFolloupwdate" id="edit_NetFolloupwdate" class="form-control">
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label">Comments</label>
                            <textarea name="strComments" id="edit_strComments" class="form-control" rows="4" placeholder="Enter comments"></textarea>
                        </div>
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

        $('.record-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete selected histories?')) {
            $.ajax({
                url: "{{ route('store.leads.histories.bulk-delete', $lead->iLeadId) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    });

    $('.edit-history-btn').on('click', function () {
        let id = $(this).data('id');
        let status = $(this).data('status');
        let followup = $(this).data('followup');
        let comments = $(this).data('comments');

        $('#edit_iStatus').val(status);
        $('#edit_NetFolloupwdate').val(followup);
        $('#edit_strComments').val(comments);
        $('#editHistoryForm').attr('action', "{{ url('store-manager/leads/' . $lead->iLeadId . '/histories') }}/" + id + "/update");

        $('#editHistoryModal').modal('show');
    });
});
</script>
@endsection