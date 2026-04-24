@extends('layouts.app')
@section('title', 'Lead History')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lead History - {{ $lead->strLeadNo }}</h4>
        <a href="{{ route('admin.reports.leads.show', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card mb-3"><div class="card-body py-2">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.reports.leads.histories', $lead->iLeadId) }}">
            <div class="col-md-3">
                <label class="form-label mb-1 small">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">All</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success btn-sm">Search</button>
                <a href="{{ route('admin.reports.leads.histories', $lead->iLeadId) }}" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive"><table class="table table-bordered mb-0">
        <thead class="table-light"><tr><th>#</th><th>Status</th><th>Comments</th><th>Follow-up Date</th><th>Entry Date</th><th>Entered By</th></tr></thead>
        <tbody>
            @forelse($histories as $history)
                <tr>
                    <td>{{ $histories->firstItem() + $loop->index }}</td>
                    <td>{{ $history->iStatus }}</td>
                    <td style="white-space: pre-line;">{{ $history->strComments ?: '—' }}</td>
                    <td>
                        {{ $history->NetFolloupwdate ? \Carbon\Carbon::parse($history->NetFolloupwdate)->format('d-m-Y') : '—' }}
                    </td>

                    <td>
                        {{ $history->EntryDate ? \Carbon\Carbon::parse($history->EntryDate)->format('d-m-Y') : '—' }}
                    </td>
                    <td>{{ optional($history->user)->strUserName ?: optional($history->user)->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No history found.</td></tr>
            @endforelse
        </tbody>
    </table></div><div class="p-3">{{ $histories->links() }}</div></div>
</div></div></div>
@endsection
