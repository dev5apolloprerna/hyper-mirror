<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoicePdfSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoicePdfSettingController extends Controller
{
    public function edit(): View
    {
        $setting = InvoicePdfSetting::query()->first();

        return view('admin.invoice-settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'terms_and_conditions' => ['nullable', 'string'],
            'bank_details' => ['nullable', 'string'],
        ]);

        $setting = InvoicePdfSetting::query()->first() ?? new InvoicePdfSetting();
        $setting->fill($data);
        $setting->save();

        return redirect()
            ->route('admin.invoice-settings.edit')
            ->with('success', 'Invoice PDF settings updated successfully.');
    }
}
