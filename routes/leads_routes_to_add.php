<?php

// -------------------------------------------------------
// ADD THESE ROUTES inside your existing auth middleware group
// in routes/web.php
// -------------------------------------------------------

// Lead detail + status change — accessible to all CRM roles
Route::middleware(['auth', 'crmrole:storemanager,measurement,production,dispatch,fitting,account'])
    ->group(function () {

        // View lead detail page
        Route::get('/leads/{leadId}', [
            \App\Http\Controllers\LeadStatusController::class, 'show'
        ])->name('leads.show');

        // Submit status change
        Route::patch('/leads/{leadId}/status', [
            \App\Http\Controllers\LeadStatusController::class, 'updateStatus'
        ])->name('leads.updateStatus');

    });
