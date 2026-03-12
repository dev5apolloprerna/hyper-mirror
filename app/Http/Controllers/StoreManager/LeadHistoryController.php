<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadHistoryController extends Controller
{
    public function index(Lead $lead)
    {
        $histories = LeadHistory::with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('store-manager.lead-histories.index', compact('lead', 'histories'));
    }

    public function store(Request $request, Lead $lead)
    {
        $request->validate([
            'iStatus' => 'required|string|max:50',
            'NetFolloupwdate' => 'nullable|date',
            'strComments' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            LeadHistory::create([
                'iLeadId' => $lead->iLeadId,
                'strComments' => $request->strComments,
                'NetFolloupwdate' => $request->NetFolloupwdate,
                'iStatus' => $request->iStatus,
                'iEnterBy' => auth()->id(),
                'EntryDate' => now(),
            ]);

            $lead->update([
                'iCurrentLeadStatus' => $request->iStatus,
                'NetFollowupdate' => $request->NetFolloupwdate,
            ]);

            DB::commit();

            return redirect()->route('store.leads.histories.index', $lead->iLeadId)
                ->with('success', 'Lead history added successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, Lead $lead, LeadHistory $history)
    {
        if ($history->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $request->validate([
            'iStatus' => 'required|string|max:50',
            'NetFolloupwdate' => 'nullable|date',
            'strComments' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $history->update([
                'strComments' => $request->strComments,
                'NetFolloupwdate' => $request->NetFolloupwdate,
                'iStatus' => $request->iStatus,
            ]);

            $latestHistory = LeadHistory::where('iLeadId', $lead->iLeadId)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestHistory) {
                $lead->update([
                    'iCurrentLeadStatus' => $latestHistory->iStatus,
                    'NetFollowupdate' => $latestHistory->NetFolloupwdate,
                ]);
            }

            DB::commit();

            return redirect()->route('store.leads.histories.index', $lead->iLeadId)
                ->with('success', 'Lead history updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function destroy(Lead $lead, LeadHistory $history)
    {
        if ($history->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $history->delete();

        $latestHistory = LeadHistory::where('iLeadId', $lead->iLeadId)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestHistory) {
            $lead->update([
                'iCurrentLeadStatus' => $latestHistory->iStatus,
                'NetFollowupdate' => $latestHistory->NetFolloupwdate,
            ]);
        }

        return redirect()->route('store.leads.histories.index', $lead->iLeadId)
            ->with('success', 'Lead history deleted successfully.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:lead_histories,id',
        ]);

        LeadHistory::where('iLeadId', $lead->iLeadId)
            ->whereIn('id', $request->ids)
            ->delete();

        $latestHistory = LeadHistory::where('iLeadId', $lead->iLeadId)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestHistory) {
            $lead->update([
                'iCurrentLeadStatus' => $latestHistory->iStatus,
                'NetFollowupdate' => $latestHistory->NetFolloupwdate,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected lead histories deleted successfully.'
        ]);
    }
}