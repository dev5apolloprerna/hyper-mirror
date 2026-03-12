<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmRole;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CrmUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with(['crmRole', 'showrooms'])->latest()->paginate(10),
            'roles' => CrmRole::orderBy('strRole')->get(),
            'showrooms' => Showroom::orderBy('strShowRoomName')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'strUserName' => 'required|string|max:50',
            'strUserMobile' => 'required|string|max:15|unique:users,strUserMobile',
            'password' => 'required|string|min:6',
            'strUserAddress' => 'nullable|string',
            'iRoalId' => 'required|exists:crm_roles,iRoleId',
            'showrooms' => 'nullable|array',
            'showrooms.*' => 'exists:showrooms,iShowroomId',
        ]);

        $user = User::create([
            'first_name' => $data['strUserName'],
            'email' => $data['strUserMobile'] . '@crm.local',
            'password' => Hash::make($data['password']),
            'mobile_number' => $data['strUserMobile'],
            'strUserName' => $data['strUserName'],
            'strUserMobile' => $data['strUserMobile'],
            'strUserAddress' => $data['strUserAddress'] ?? null,
            'iRoalId' => $data['iRoalId'],
            'role_id' => 2,
        ]);

        $user->showrooms()->sync($data['showrooms'] ?? []);

        return back()->with('success', 'CRM user created.');
    }
}
