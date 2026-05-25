<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\LeadPayment;
use App\Support\LeadWorkflow;
use App\Models\QuotationCancelReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class LeadHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Lead $lead)
    {
        $isAdminLike = blank(optional(auth()->user()->crmRole)->slug);

        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
            optional(auth()->user()->crmRole)->slug === 'storemanager' ||
            $isAdminLike,
            403
        );

        $histories = LeadHistory::with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->orderBy('id', 'desc')
            ->paginate(15);

        $roleSlug = optional(auth()->user()->crmRole)->slug;

        // Determine allowed statuses for the status-change form
        $allowedStatuses = $isAdminLike ? [] : LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);

        $lead->load(['customer', 'quotation', 'quotations.product', 'quotations.category', 'quotations.shape', 'createdBy', 'designs']);
        $canViewFinancial = (bool) auth()->user()->can_view_financial;

        // For "Deal Done": check if payments match quotation amount
        $canCloseDeal = false;
        if (in_array(LeadWorkflow::STATUS_DEAL_DONE, $allowedStatuses)) {
            $totalPaid            = (float) $lead->payments()->sum('iPaidAmount');
            $leadDiscountAmount   = (int) ($lead->isDiscountApplicable ?? 0) === 1 ? (float) ($lead->decDiscountAmount ?? 0) : 0;
            $paymentDiscountAmount = (float) $lead->payments()->sum('iDiscountAmount');
            $totalSettled         = $totalPaid + $leadDiscountAmount + $paymentDiscountAmount;
            $leadAmount     = (float) $lead->iLeadAmount;
            $canCloseDeal   = $leadAmount > 0 && abs($totalSettled - $leadAmount) < 0.01;
            // Remove Deal Done from allowed if payment mismatch
            if (!$canCloseDeal) {
                $allowedStatuses = array_values(array_filter(
                    $allowedStatuses,
                    fn($s) => $s !== LeadWorkflow::STATUS_DEAL_DONE
                ));
            }
        }

        $isReadOnly = $isAdminLike || LeadWorkflow::readOnlyForRole((string) $roleSlug, $lead->iCurrentLeadStatus)
            || in_array($lead->iCurrentLeadStatus, LeadWorkflow::terminalStatuses());

        $cancelReasons = QuotationCancelReason::query()
            ->where('isDelete', 0)
            ->where('iStatus', 1)
            ->orderBy('reason')
            ->get(['id', 'reason']);

        return view('store-manager.lead-histories.index', compact(
            'lead',
            'histories',
            'allowedStatuses',
            'roleSlug',
            'isReadOnly',
            'canCloseDeal',
            'canViewFinancial',
            'cancelReasons'
        ));
    }

    public function store(Request $request, Lead $lead)
    {
        abort_if(blank(optional(auth()->user()->crmRole)->slug), 403, 'Admin has view-only access.');
        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
            optional(auth()->user()->crmRole)->slug === 'storemanager',
            403
        );

        // Prevent changes to terminal statuses
        if (in_array($lead->iCurrentLeadStatus, LeadWorkflow::terminalStatuses())) {
            return back()->with('error', 'This lead is closed and cannot be updated.');
        }

        $isRejection = $request->iStatus === LeadWorkflow::STATUS_LEAD_REJECTED;
        $isDealDone  = $request->iStatus === LeadWorkflow::STATUS_DEAL_DONE;
        $isQuotationApproved = $request->iStatus === LeadWorkflow::STATUS_QUOTATION_APPROVED;
        $shouldClearFollowup = in_array($request->iStatus, [
            // LeadWorkflow::STATUS_DISPATCHED,
            LeadWorkflow::STATUS_DISPATCHED_DONE,
            LeadWorkflow::STATUS_RECEIVED_AT_NAROL,
        ], true);

        $rules = [
            'iStatus'        => 'required|string|in:' . implode(',', LeadWorkflow::allStatuses()),
            'strComments'    => 'required|string|max:2000',
        ];

        // Follow-up date: required only for certain statuses, NOT for rejection or measurement done
        if (!$isRejection && !$isDealDone && $request->filled('iStatus')) {
            $needsFollowup = in_array($request->iStatus, LeadWorkflow::followupRequiredStatuses(), true);
            if ($needsFollowup) {
                $rules['NetFolloupwdate'] = 'required|date';
            } else {
                $rules['NetFolloupwdate'] = 'nullable|date';
            }
        } else {
            $rules['NetFolloupwdate'] = 'nullable|date';
        }

        // Rejection reason required
        if ($isRejection) {
            //$rules['rejection_reason'] = 'required|string|max:500';
            $rules['rejection_reason_id'] = 'required|integer|exists:quotation_cancel_reasons,id';
            $rules['rejection_reason_note'] = 'nullable|string|max:500';
        }

         if ($isQuotationApproved) {
            $rules['expected_delivery_date'] = 'required|date';
        }

        $request->validate($rules);

        $roleSlug = optional(auth()->user()->crmRole)->slug;

        // Determine which statuses this user can set
        $allowedStatuses = $roleSlug === 'storemanager'
            ? (LeadWorkflow::transitionMap()[$lead->iCurrentLeadStatus] ?? [])
            : LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);

        if (!in_array($request->iStatus, $allowedStatuses, true)) {
            return back()->withInput()->with('error', 'Selected status is not allowed for your role.');
        }

        // "Deal Done" payment check
        if ($isDealDone) {
            $totalPaid             = (float) $lead->payments()->sum('iPaidAmount');
            $leadDiscountAmount    = (int) ($lead->isDiscountApplicable ?? 0) === 1 ? (float) ($lead->decDiscountAmount ?? 0) : 0;
            $paymentDiscountAmount = (float) $lead->payments()->sum('iDiscountAmount');
            $totalSettled          = $totalPaid + $leadDiscountAmount + $paymentDiscountAmount;
            $leadAmount     = (float) $lead->iLeadAmount;
            if ($leadAmount <= 0 || abs($totalSettled - $leadAmount) >= 0.01) {
                return back()->withInput()->with('error',
                    //'Cannot mark as Deal Done. Total payments (₹' . number_format($totalPaid, 2) .
                    'Cannot mark as Deal Done. Payments + discount (₹' . number_format($totalSettled, 2) .
                    ') must equal the lead amount (₹' . number_format($leadAmount, 2) . ').'
                );
            }
        }

        DB::beginTransaction();

        try {
            $comments = $request->strComments;

            // Append rejection reason to comments if rejection
            if ($isRejection) {
                $reasonName = QuotationCancelReason::query()
                    ->where('isDelete', 0)
                    ->where('iStatus', 1)
                    ->whereKey($request->rejection_reason_id)
                    ->value('reason');

                if (!empty($reasonName)) {
                    $comments = 'Rejection Reason: ' . $reasonName . "\n" . $comments;
                }

                if ($request->filled('rejection_reason_note')) {
                    $comments = 'Rejection Note: ' . $request->rejection_reason_note . "\n" . $comments;
                }
            }

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
             //   'strComments'     => $comments,
                'strComments'     => $isQuotationApproved
                    ? $comments . "\nExpected Delivery Date: " . $request->expected_delivery_date
                    : $comments,
               // 'NetFolloupwdate' => $isRejection ? null : $request->NetFolloupwdate,
                'NetFolloupwdate' => ($isRejection || $shouldClearFollowup) ? null : $request->NetFolloupwdate,
                'iStatus'         => $request->iStatus,
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);

            //$lead->update([
            $leadUpdateData = [
                'iCurrentLeadStatus' => $request->iStatus,
                //'NetFollowupdate'    => $isRejection ? null : $request->NetFolloupwdate,
                'NetFollowupdate'    => ($isRejection || $shouldClearFollowup) ? null : $request->NetFolloupwdate,
                'expected_delivery_date' => $isQuotationApproved ? $request->expected_delivery_date : $lead->expected_delivery_date,
             ];

            if (Schema::hasColumn('leads', 'DispatchedDate')) {
                //$leadUpdateData['DispatchedDate'] = $request->iStatus === LeadWorkflow::STATUS_DISPATCHED
                 $leadUpdateData['DispatchedDate'] = in_array($request->iStatus, [
                    LeadWorkflow::STATUS_DISPATCHED_DONE,
                    LeadWorkflow::STATUS_RECEIVED_AT_NAROL,
                ], true)
                    ? now()->toDateString()
                    : $lead->DispatchedDate;
            }

            $lead->update($leadUpdateData);
            DB::commit();

            return redirect()->route('store.leads.index')->with('success', 'Quotation status changed successfully.');

            //return redirect()->route('store.leads.histories.index', $lead->iLeadId)->with('success', 'Lead updated to "' . $request->iStatus . '" successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, Lead $lead, LeadHistory $history)
    {
        abort(403, 'History records cannot be edited.');
    }

    public function destroy(Lead $lead, LeadHistory $history)
    {
        abort(403, 'History records cannot be deleted.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
        abort(403, 'History records cannot be deleted.');
    }
    public function uploadFittingImages(Request $request, Lead $lead)
    {
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        abort_unless(in_array($roleSlug, ['fitting', 'storemanager'], true), 403);

        $request->validate([
            'fitting_images' => 'required|array|min:1|max:6',
            'fitting_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'strComments' => 'nullable|string|max:1000',
        ]);

        $destinationPath = public_path('uploads/lead-fitting-images');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }

        $uploadedImagePaths = [];
        foreach ($request->file('fitting_images') as $image) {
            $filename = $lead->iLeadId . '_' . now()->format('YmdHis') . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);
            $uploadedImagePaths[] = 'public/uploads/lead-fitting-images/' . $filename;
        }

        $comments = trim((string) $request->strComments);
        if ($comments === '') {
            $comments = 'Fitting images uploaded from listing.';
        }
        $comments .= "\n[Fitting Images] " . implode('|', $uploadedImagePaths);

        LeadHistory::create([
            'iLeadId' => $lead->iLeadId,
            'strComments' => $comments,
            'NetFolloupwdate' => $lead->NetFollowupdate,
            'iStatus' => $lead->iCurrentLeadStatus,
            'iEnterBy' => auth()->id(),
            'EntryDate' => now(),
        ]);

        return back()->with('success', 'Fitting images uploaded successfully.');
    }
        public function deleteFittingImage(Request $request, Lead $lead, LeadHistory $history)
    {
        $roleSlug = optional(auth()->user()->crmRole)->slug;
        abort_unless(in_array($roleSlug, ['fitting', 'storemanager'], true), 403);
        abort_unless((int) $history->iLeadId === (int) $lead->iLeadId, 404);

        $request->validate([
            'image_path' => 'required|string',
        ]);

        $rawComments = (string) ($history->strComments ?? '');
        $marker = '[Fitting Images]';
        if (strpos($rawComments, $marker) === false) {
            return back()->with('error', 'No fitting images found in this history record.');
        }

        [$commentText, $imagesPart] = array_pad(explode($marker, $rawComments, 2), 2, '');
        $paths = [];
        foreach (explode('|', trim($imagesPart)) as $item) {
            $trimmed = trim($item);
            if ($trimmed !== '') {
                $paths[] = $trimmed;
            }
        }

        $imagePath = trim((string) $request->image_path);
        $remaining = array_values(array_filter($paths, function ($path) use ($imagePath) {
            return $path !== $imagePath;
        }));

        if (count($remaining) === count($paths)) {
            return back()->with('error', 'Selected image was not found.');
        }

        $absolutePath = public_path(str_replace('public/', '', $imagePath));
        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }

        $updatedComments = trim($commentText);
        if (!empty($remaining)) {
            $updatedComments .= ($updatedComments !== '' ? "\n" : '') . $marker . ' ' . implode('|', $remaining);
        }
        $history->strComments = $updatedComments;
        $history->save();

        return back()->with('success', 'Fitting image deleted successfully.');
    }
}