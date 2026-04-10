<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmRole;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CrmUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['crmRole', 'showrooms'])
            ->whereNotNull('iRoalId');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($sub) use ($search) {
                $sub->where('strUserName', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('strUserMobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('crmRole', fn($q) => $q->where('strRole', 'like', "%{$search}%"))
                    ->orWhereHas('showrooms', fn($q) => $q->where('strShowRoomName', 'like', "%{$search}%"));
            });
        }

        return view('admin.users.index', [
            'users'     => $query->latest()->paginate(10)->withQueryString(),
            'showrooms' => Showroom::orderBy('strShowRoomName')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(),
            'roles' => CrmRole::orderBy('strRole')->get(),
            'showrooms' => Showroom::orderBy('strShowRoomName')->get(),
            'isEdit' => false,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate($this->userRules(), $this->userMessages());

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'first_name'       => $data['strUserName'],
                'email'            => $data['email'],
                'password'         => Hash::make($data['password']),
                'mobile_number'    => $data['strUserMobile'],
                'strUserName'      => $data['strUserName'],
                'strUserMobile'    => $data['strUserMobile'],
                'strUserAddress'   => $data['strUserAddress'] ?? null,
                'iRoalId'          => $data['iRoalId'],
                'can_view_financial' => $request->input('can_view_financial', 0),
                'role_id'          => 2,
                'status'           => 1,
            ]);

            $user->showrooms()->sync($data['showrooms']);
        });

        return redirect()->route('admin.users.index')->with('success', 'CRM user created successfully.');
    }
    public function edit(User $user)
    {
        $user->load('showrooms');

        return view('admin.users.form', [
            'user' => $user,
            'roles' => CrmRole::orderBy('strRole')->get(),
            'showrooms' => Showroom::orderBy('strShowRoomName')->get(),
            'isEdit' => true,
        ]);
    }


    public function update(Request $request, User $user)
    {
        $data   = $request->validate($this->userRules($user->id), $this->userMessages());
        $status = $request->boolean('status') ? 1 : 0;

        DB::transaction(function () use ($data, $user, $status, $request) {
            $user->update([
                'first_name'         => $data['strUserName'],
                'email'              => $data['email'],
                'mobile_number'      => $data['strUserMobile'],
                'strUserName'        => $data['strUserName'],
                'strUserMobile'      => $data['strUserMobile'],
                'strUserAddress'     => $data['strUserAddress'] ?? null,
                'iRoalId'            => $data['iRoalId'],
                'can_view_financial' => $request->input('can_view_financial', 0),
                'status'             => $status,
            ]);

            $user->showrooms()->sync($data['showrooms']);
        });

        return redirect()->route('admin.users.index')->with('success', 'CRM user updated successfully.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('admin.users.index')->with('success', 'Password updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            $user->showrooms()->detach();
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'CRM user deleted successfully.');
    }

    protected function userRules(?int $userId = null): array
    {
        $rules = [
            'strUserName'    => 'required|string|max:50',
            'email'          => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'strUserMobile'  => [
                'required', 'string', 'max:15',
                Rule::unique('users', 'strUserMobile')->ignore($userId),
            ],
            'strUserAddress' => 'nullable|string|max:255',
            'iRoalId'        => 'required|exists:crm_roles,iRoleId',
            'showrooms'      => 'required|array|min:1',
            'showrooms.*'    => 'required|integer|exists:showrooms,iShowroomId|distinct',
        ];

        if ($userId === null) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        return $rules;
    }

    protected function userMessages(): array
    {
        return [
            'email.required'   => 'Email is required.',
            'email.email'      => 'Please enter a valid email address.',
            'email.unique'     => 'This email is already in use.',
            'showrooms.required' => 'Please select at least one showroom.',
            'showrooms.min'    => 'Please select at least one showroom.',
        ];
    }
}
