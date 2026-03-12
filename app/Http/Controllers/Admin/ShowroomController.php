<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShowroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Showroom::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('strShowRoomName', 'like', "%{$search}%");
        }

        $showrooms = $query->orderBy('iShowroomId', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.showroom.index', compact('showrooms'));
    }

    public function create()
    {
        return view('admin.showroom.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'strShowRoomName' => 'required|string|max:50|unique:showrooms,strShowRoomName',
        ]);

        Showroom::create([
            'strShowRoomName' => $request->strShowRoomName,
        ]);

        return redirect()->route('admin.showroom.index')
            ->with('success', 'Showroom added successfully.');
    }

    public function edit($id)
    {
        $showroom = Showroom::findOrFail($id);
        return view('admin.showroom.form', compact('showroom'));
    }

    public function update(Request $request, $id)
    {
        $showroom = Showroom::findOrFail($id);

        $request->validate([
            'strShowRoomName' => [
                'required',
                'string',
                'max:50',
                Rule::unique('showrooms', 'strShowRoomName')->ignore($showroom->iShowroomId, 'iShowroomId')
            ],
        ]);

        $showroom->update([
            'strShowRoomName' => $request->strShowRoomName,
        ]);

        return redirect()->route('admin.showroom.index')
            ->with('success', 'Showroom updated successfully.');
    }

    public function destroy($id)
    {
        $showroom = Showroom::findOrFail($id);
        $showroom->delete();

        return redirect()->route('admin.showroom.index')
            ->with('success', 'Showroom deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:showrooms,iShowroomId',
        ]);

        Showroom::whereIn('iShowroomId', $request->ids)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected showrooms deleted successfully.'
        ]);
    }
}
