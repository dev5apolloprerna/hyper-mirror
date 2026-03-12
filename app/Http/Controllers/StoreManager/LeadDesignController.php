<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LeadDesignController extends Controller
{
    public function index(Lead $lead)
    {
        $designs = LeadDesign::where('iLeadId', $lead->iLeadId)
            ->orderBy('iLeadDesignId', 'desc')
            ->paginate(15);

        return view('store-manager.lead-designs.index', compact('lead', 'designs'));
    }

    public function create(Lead $lead)
    {
        return view('store-manager.lead-designs.create', compact('lead'));
    }

    public function store(Request $request, Lead $lead)
    {
        $request->validate([
            'strTitle' => 'nullable|string|max:100',
            'strFilename' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $file = $request->file('strFilename');
        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $destinationPath = public_path('uploads/lead-designs');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $file->move($destinationPath, $fileName);

        LeadDesign::create([
            'iLeadId' => $lead->iLeadId,
            'strFilename' => $fileName,
            'strTitle' => $request->strTitle,
        ]);

        return redirect()->route('store.leads.designs.index', $lead->iLeadId)
            ->with('success', 'Lead design uploaded successfully.');
    }

    public function edit(Lead $lead, LeadDesign $design)
    {
        if ($design->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        return view('store-manager.lead-designs.edit', compact('lead', 'design'));
    }

    public function update(Request $request, Lead $lead, LeadDesign $design)
    {
        if ($design->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $request->validate([
            'strTitle' => 'nullable|string|max:100',
            'strFilename' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
        ]);

        $fileName = $design->strFilename;
        $destinationPath = public_path('uploads/lead-designs');

        if ($request->hasFile('strFilename')) {
            $oldFile = $destinationPath . '/' . $design->strFilename;
            if (File::exists($oldFile)) {
                unlink($oldFile);
            }

            $file = $request->file('strFilename');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($destinationPath, $fileName);
        }

        $design->update([
            'strTitle' => $request->strTitle,
            'strFilename' => $fileName,
        ]);

        return redirect()->route('store.leads.designs.index', $lead->iLeadId)
            ->with('success', 'Lead design updated successfully.');
    }

    public function destroy(Lead $lead, LeadDesign $design)
    {
        if ($design->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $filePath = public_path('uploads/lead-designs/' . $design->strFilename);

        if (File::exists($filePath)) {
            unlink($filePath);
        }

        $design->delete();

        return redirect()->route('store.leads.designs.index', $lead->iLeadId)
            ->with('success', 'Lead design deleted successfully.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:lead_designs,iLeadDesignId',
        ]);

        $designs = LeadDesign::where('iLeadId', $lead->iLeadId)
            ->whereIn('iLeadDesignId', $request->ids)
            ->get();

        foreach ($designs as $design) {
            $filePath = public_path('uploads/lead-designs/' . $design->strFilename);
            if (File::exists($filePath)) {
                unlink($filePath);
            }
            $design->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected lead designs deleted successfully.'
        ]);
    }
}
