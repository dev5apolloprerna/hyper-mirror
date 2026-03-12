<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\User;
use App\Models\UserShowroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserShowroomController extends Controller
{
    public function index(Request $request)
    {
        $query = UserShowroom::with(['user', 'showroom']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('strUserName', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('strUserMobile', 'like', "%{$search}%");
                })->orWhereHas('showroom', function ($sub) use ($search) {
                    $sub->where('strShowRoomName', 'like', "%{$search}%");
                });
            });
        }

        $userShowrooms = $query->orderBy('UserShowRoomId', 'desc')
            ->paginate(10)
            ->withQueryString();

        $users = User::orderBy('strUserName', 'asc')->get();
        $showrooms = Showroom::orderBy('strShowRoomName', 'asc')->get();

        return view('admin.user-showroom.index', compact('userShowrooms', 'users', 'showrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'UserId' => 'required|exists:users,id',
            'ShowRoomId' => [
                'required',
                'exists:showrooms,iShowroomId',
                Rule::unique('user_showrooms')->where(function ($query) use ($request) {
                    return $query->where('UserId', $request->UserId)
                                 ->where('ShowRoomId', $request->ShowRoomId);
                }),
            ],
        ], [
            'ShowRoomId.unique' => 'This user and showroom combination already exists.',
        ]);

        UserShowroom::create([
            'UserId' => $request->UserId,
            'ShowRoomId' => $request->ShowRoomId,
        ]);

        return redirect()->route('admin.user-showroom.index')
            ->with('success', 'User showroom added successfully.');
    }

    public function update(Request $request, $id)
    {
        $userShowroom = UserShowroom::findOrFail($id);

        $request->validate([
            'UserId' => 'required|exists:users,id',
            'ShowRoomId' => [
                'required',
                'exists:showrooms,iShowroomId',
                Rule::unique('user_showrooms')->where(function ($query) use ($request) {
                    return $query->where('UserId', $request->UserId)
                                 ->where('ShowRoomId', $request->ShowRoomId);
                })->ignore($userShowroom->UserShowRoomId, 'UserShowRoomId'),
            ],
        ], [
            'ShowRoomId.unique' => 'This user and showroom combination already exists.',
        ]);

        $userShowroom->update([
            'UserId' => $request->UserId,
            'ShowRoomId' => $request->ShowRoomId,
        ]);

        return redirect()->route('admin.user-showroom.index')
            ->with('success', 'User showroom updated successfully.');
    }

    public function destroy($id)
    {
        $userShowroom = UserShowroom::findOrFail($id);
        $userShowroom->delete();

        return redirect()->route('admin.user-showroom.index')
            ->with('success', 'User showroom deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:user_showrooms,UserShowRoomId',
        ]);

        UserShowroom::whereIn('UserShowRoomId', $request->ids)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected user showroom records deleted successfully.'
        ]);
    }
}