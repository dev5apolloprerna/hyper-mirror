<?php
 
 namespace App\Http\Controllers\StoreManager;
 
 use App\Http\Controllers\Controller;
 use App\Models\Lead;
 use App\Models\LeadHistory;
use App\Support\LeadWorkflow;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;
 
 class LeadHistoryController extends Controller
 {
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index(Lead $lead)
     {
        abort_unless(LeadWorkflow::canAccessLead(auth()->user(), $lead) || optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $histories = LeadHistory::with('user')
             ->where('iLeadId', $lead->iLeadId)
             ->orderBy('id', 'desc')
             ->paginate(15);
 
        $allowedStatuses = LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);
        if (optional(auth()->user()->crmRole)->slug === 'storemanager') {
            $allowedStatuses = LeadWorkflow::allStatuses();
        }

        return view('store-manager.lead-histories.index', compact('lead', 'histories', 'allowedStatuses'));
     }
 
     public function store(Request $request, Lead $lead)
     {
        abort_unless(LeadWorkflow::canAccessLead(auth()->user(), $lead) || optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $request->validate([
            'iStatus' => 'required|string|in:' . implode(',', LeadWorkflow::allStatuses()),
             'NetFolloupwdate' => 'nullable|date',
            'strComments' => 'required|string',
         ]);
 
        $allowedStatuses = optional(auth()->user()->crmRole)->slug === 'storemanager'
            ? LeadWorkflow::allStatuses()
            : LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);

        if (! in_array($request->iStatus, $allowedStatuses, true)) {
            return back()->withInput()->with('error', 'Selected status is not allowed for your role.');
        }

        if (in_array($request->iStatus, LeadWorkflow::followupRequiredStatuses(), true) && ! $request->NetFolloupwdate) {
            return back()->withInput()->withErrors(['NetFolloupwdate' => 'Next follow up date is required for the selected status.']);
        }

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
        abort(403);
     }
 
     public function destroy(Lead $lead, LeadHistory $history)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         if ($history->iLeadId != $lead->iLeadId) {
             abort(404);
         }
 
        $this->syncLeadWithLatestHistory($lead);
 
         return redirect()->route('store.leads.histories.index', $lead->iLeadId)
             ->with('success', 'Lead history deleted successfully.');
     }
 
     public function bulkDelete(Request $request, Lead $lead)
     {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);

         $request->validate([
             'ids' => 'required|array',
             'ids.*' => 'required|integer|exists:lead_histories,id',
         ]);
 
         LeadHistory::where('iLeadId', $lead->iLeadId)
             ->whereIn('id', $request->ids)
             ->delete();
 
        $this->syncLeadWithLatestHistory($lead);

        return response()->json([
            'status' => true,
            'message' => 'Selected lead histories deleted successfully.'
        ]);
    }

    private function syncLeadWithLatestHistory(Lead $lead): void
    {
         $latestHistory = LeadHistory::where('iLeadId', $lead->iLeadId)
             ->orderBy('id', 'desc')
             ->first();
 
         if ($latestHistory) {
             $lead->update([
                 'iCurrentLeadStatus' => $latestHistory->iStatus,
                 'NetFollowupdate' => $latestHistory->NetFolloupwdate,
             ]);
         }
     }
}
