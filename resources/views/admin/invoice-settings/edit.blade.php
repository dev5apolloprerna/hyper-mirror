@extends('layouts.app')

@section('title', 'Invoice PDF Settings')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @include('common.alert')

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Invoice PDF Settings</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Manage Terms & Bank Details</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.invoice-settings.update') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Terms and Conditions</label>
                                    <textarea id="terms_and_conditions"
                                        name="terms_and_conditions"
                                        class="form-control"
                                        rows="9"
                                        placeholder="Enter invoice terms and conditions...">{{ old('terms_and_conditions', $setting->terms_and_conditions ?? '') }}</textarea>
                                    @error('terms_and_conditions')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bank Details</label>
                                    <textarea id="bank_details"
                                        name="bank_details"
                                        class="form-control"
                                        rows="7"
                                        placeholder="Enter bank details to show in invoice PDF...">{{ old('bank_details', $setting->bank_details ?? '') }}</textarea>
                                    @error('bank_details')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ['terms_and_conditions', 'bank_details'].forEach(function(editorId) {
            const element = document.getElementById(editorId);
            if (!element) {
                return;
            }

            ClassicEditor
                .create(element, {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'undo', 'redo'
                    ]
                })
                .catch(function(error) {
                    console.error('CKEditor init failed for ' + editorId, error);
                });
        });
    </script>
@endsection