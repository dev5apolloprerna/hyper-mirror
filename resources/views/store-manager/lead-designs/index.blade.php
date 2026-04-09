@extends('layouts.app')

@section('title', 'Lead Designs')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Lead Designs</h4>
                            <p class="mb-0 mt-1"><strong>Lead No:</strong> {{ $lead->strLeadNo }}</p>
                        </div>
                        <div class="page-title-right">
                            <a href="{{ route('store.leads.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <!-- <a href="{{ route('store.leads.designs.create', $lead->iLeadId) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Design
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>

                       <div class="row g-3">
                <div class="col-xl-4 col-lg-5">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas {{ $editDesign ? 'fa-pen' : 'fa-plus-circle' }} me-2"></i>
                                {{ $editDesign ? 'Edit Design' : 'Upload Design' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ $editDesign ? route('store.leads.designs.update', [$lead->iLeadId, $editDesign->iLeadDesignId]) : route('store.leads.designs.store', $lead->iLeadId) }}"
                                  method="POST"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text"
                                           name="strTitle"
                                           class="form-control"
                                           value="{{ old('strTitle', optional($editDesign)->strTitle) }}"
                                           placeholder="Enter design title">
                                    @if($errors->has('strTitle'))
                                        <span class="text-danger small">{{ $errors->first('strTitle') }}</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Design File {!! $editDesign ? '' : '<span class="text-danger">*</span>' !!}
                                    </label>
                                    <input type="file" name="strFilename" class="form-control">
                                    @if($errors->has('strFilename'))
                                        <span class="text-danger small">{{ $errors->first('strFilename') }}</span>
                                    @endif
                                    @if($editDesign && $editDesign->strFilename)
                                        <div class="mt-2 small">
                                            Current:
                                            <a href="{{ asset('uploads/lead-designs/' . $editDesign->strFilename) }}" target="_blank">
                                                {{ $editDesign->strFilename }}
                                            </a>

                                            </a>

                                              </div>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas {{ $editDesign ? 'fa-save' : 'fa-upload' }} me-1"></i>
                                        {{ $editDesign ? 'Update Design' : 'Upload Design' }}
                                    </button>
                                    @if($editDesign)
                                        <a href="{{ route('store.leads.designs.index', $lead->iLeadId) }}" class="btn btn-light border">
                                            Cancel
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                                    <div class="col-xl-8 col-lg-7">
                    <div class="card h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-images me-2"></i> Uploaded Designs
                            </h5>
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
                                            <th>Title</th>
                                            <th>File</th>
                                            <th width="14%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($designs as $design)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="record-checkbox" value="{{ $design->iLeadDesignId }}">
                                                </td>
                                                <td>{{ $design->strTitle ?: '—' }}</td>
                                                <td>
                                                    <a href="{{ asset('uploads/lead-designs/' . $design->strFilename) }}" target="_blank">
                                                        {{ $design->strFilename }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ asset('uploads/lead-designs/' . $design->strFilename) }}" target="_blank" class="text-info me-2" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <a href="{{ route('store.leads.designs.edit', [$lead->iLeadId, $design->iLeadDesignId]) }}"
                                                       class="text-primary me-2"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="javascript:void(0);" class="text-danger delete-record" data-id="{{ $design->iLeadDesignId }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $design->iLeadDesignId }}" action="{{ route('store.leads.designs.delete', [$lead->iLeadId, $design->iLeadDesignId]) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No designs found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                {{ $designs->links() }}
                            </div>
                        </div>
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

        if (confirm('Are you sure you want to delete this design?')) {
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

        if (confirm('Are you sure you want to delete selected designs?')) {
            $.ajax({
                url: "{{ route('store.leads.designs.bulk-delete', $lead->iLeadId) }}",
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
});
</script>
@endsection
