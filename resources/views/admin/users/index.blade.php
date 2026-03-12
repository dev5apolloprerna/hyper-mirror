@extends('layouts.app')
@section('title','CRM Users')
@section('content')
<div class="main-content"><div class="page-content"><div class="container-fluid">
<h4>Create CRM User</h4>
<form method="POST" action="{{ route('crm.admin.users.store') }}" class="row g-2 mb-4">@csrf
<div class="col-md-3"><input name="strUserName" class="form-control" placeholder="Name" required></div>
<div class="col-md-2"><input name="strUserMobile" class="form-control" placeholder="Mobile" required></div>
<div class="col-md-2"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
<div class="col-md-2"><select name="iRoalId" class="form-control" required><option value="">Role</option>@foreach($roles as $role)<option value="{{ $role->iRoleId }}">{{ $role->strRole }}</option>@endforeach</select></div>
<div class="col-md-3"><select name="showrooms[]" multiple class="form-control">@foreach($showrooms as $showroom)<option value="{{ $showroom->iShowroomId }}">{{ $showroom->strShowRoomName }}</option>@endforeach</select></div>
<div class="col-md-12"><textarea name="strUserAddress" class="form-control" placeholder="Address"></textarea></div>
<div class="col-md-12"><button class="btn btn-primary">Create User</button></div>
</form>
<table class="table table-bordered"><tr><th>Name</th><th>Mobile</th><th>Role</th><th>Showrooms</th></tr>
@foreach($users as $user)
<tr><td>{{ $user->strUserName ?? $user->first_name }}</td><td>{{ $user->strUserMobile }}</td><td>{{ optional($user->crmRole)->strRole }}</td><td>{{ $user->showrooms->pluck('strShowRoomName')->join(', ') }}</td></tr>
@endforeach
</table>{{ $users->links() }}
</div></div></div>
@endsection
