<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmRole;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CrmUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['crmRole', 'showrooms'])
            ->whereNotNull('iRoalId');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('strUserName', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('strUserMobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('crmRole', function ($roleQuery) use ($search) {
                        $roleQuery->where('strRole', 'like', "%{$search}%");
                    })
                    ->orWhereHas('showrooms', function ($showroomQuery) use ($search) {
                        $showroomQuery->where('strShowRoomName', 'like', "%{$search}%");
                    });
            });
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(10)->withQueryString(),
            'roles' => CrmRole::orderBy('strRole')->get(),
            'showrooms' => Showroom::orderBy('strShowRoomName')->get(),
        ]);
    }

     public function store(Request $request)
    {
        $data = $request->validate([
            'strUserName' => 'required|string|max:50',
            'strUserMobile' => 'required|string|max:15|unique:users,strUserMobile',
            'password' => 'required|string|min:6|confirmed',
            'strUserAddress' => 'nullable|string|max:255',
            'iRoalId' => 'required|exists:crm_roles,iRoleId',
            'showrooms' => 'required|array|min:1',
            'showrooms.*' => 'required|integer|exists:showrooms,iShowroomId|distinct',
        ], [
            'showrooms.required' => 'Please select at least one showroom.',
            'showrooms.min' => 'Please select at least one showroom.',
        ]);

       
        DB::transaction(function () use ($data) {
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
                'status' => 1,
            ]);

            $user->showrooms()->sync($data['showrooms']);
        });

        return back()->with('success', 'CRM user created.');
        return redirect()->route('admin.users.index')->with('success', 'CRM user created successfully with showroom access.');
    }
}
