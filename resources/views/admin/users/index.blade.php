@extends('layouts.app')
@section('title', 'CRM Users')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Admin Users</h4>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Create User</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.users.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">User Name</label>
                                    <input type="text" name="strUserName" value="{{ old('strUserName') }}" class="form-control" placeholder="Enter user name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" name="strUserMobile" value="{{ old('strUserMobile') }}" class="form-control" placeholder="Enter mobile number" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="iRoalId" class="form-select" required>
                                        <option value="">Select role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->iRoleId }}" @selected(old('iRoalId') == $role->iRoleId)>
                                                {{ $role->strRole }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assign Showrooms</label>
                                    <select name="showrooms[]" id="showroom-select2" class="form-select showroom-select2" multiple required>
                                        @foreach ($showrooms as $showroom)
                                            <option value="{{ $showroom->iShowroomId }}" @selected(collect(old('showrooms', []))->contains($showroom->iShowroomId))>
                                                {{ $showroom->strShowRoomName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Search and select multiple showrooms using the checkbox list.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="strUserAddress" class="form-control" rows="3" placeholder="Enter address">{{ old('strUserAddress') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h5 class="card-title mb-0">User List</h5>
                            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search users, roles, showrooms">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Name</th>
                                            <th>Mobile</th>
                                            <th>Role</th>
                                            <th>Showrooms</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                                <td>{{ $user->strUserName ?: $user->full_name }}</td>
                                                <td>{{ $user->strUserMobile ?: $user->mobile_number }}</td>
                                                <td>{{ optional($user->crmRole)->strRole ?? '-' }}</td>
                                                <td>
                                                    @forelse ($user->showrooms as $showroom)
                                                        <span class="badge bg-primary-subtle text-primary me-1 mb-1">{{ $showroom->strShowRoomName }}</span>
                                                    @empty
                                                        <span class="text-muted">No showroom assigned</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @if ((int) $user->status === 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No admin users found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $users->links() }}
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-results__option {
            padding: 0;
        }

        .select2-container--default .showroom-option .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            margin: 0;
        }

        .select2-container--default .showroom-option .form-check-input {
            margin-top: 0;
            pointer-events: none;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            padding: 0.35rem;
        }
    </style>

    <script>
        $(function () {
            const $showroomSelect = $('#showroom-select2');

            function showroomTemplate(option) {
                if (!option.id) {
                    return option.text;
                }

                const isSelected = option.element && option.element.selected;
                const $option = $(
                    '<div class="showroom-option">' +
                        '<div class="form-check">' +
                            '<input type="checkbox" class="form-check-input" ' + (isSelected ? 'checked' : '') + '>' +
                            '<label class="form-check-label"></label>' +
                        '</div>' +
                    '</div>'
                );

                $option.find('.form-check-label').text(option.text);
                return $option;
            }

            $showroomSelect.select2({
                placeholder: 'Select showroom',
                closeOnSelect: false,
                allowClear: true,
                width: '100%',
                templateResult: showroomTemplate,
                templateSelection: function (option) {
                    return option.text || option.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                matcher: $.fn.select2.defaults.defaults.matcher
            });

        });
    </script>
 @endsection
