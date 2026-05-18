<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LetterSetting;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LetterSettingController extends Controller
{
    public function index()
    {
        $letterSettings = $this->scopeLetterSettingsForUser(LetterSetting::query(), auth()->user())
            ->latest()
            ->get();

        return view('Admin.Backend.Letters.setting', compact('letterSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name_ar' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'authorized_sign_name' => 'required|string|max:255',
            'authorized_sign_title' => 'required|string|max:255',
            'signature_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'stamp_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'company_logo.image' => __('dashboard.company_logo') . ' ' . __('messages.must_be_image'),
            'company_logo.mimes' => __('dashboard.company_logo') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'company_logo.max' => __('dashboard.company_logo') . ' ' . __('messages.max_file_size') . ': 2MB.',
            'signature_image.image' => __('dashboard.signature_image') . ' ' . __('messages.must_be_image'),
            'signature_image.mimes' => __('dashboard.signature_image') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'signature_image.max' => __('dashboard.signature_image') . ' ' . __('messages.max_file_size') . ': 2MB.',
            'stamp_image.image' => __('dashboard.stamp_image') . ' ' . __('messages.must_be_image'),
            'stamp_image.mimes' => __('dashboard.stamp_image') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'stamp_image.max' => __('dashboard.stamp_image') . ' ' . __('messages.max_file_size') . ': 2MB.',
        ]);

        // Prepare data
        $data = $request->only([
            'company_name_ar',
            'authorized_sign_name',
            'authorized_sign_title',
        ]);
        $data['company_id'] = $this->tenantIdForUser($request->user());

        // Handle file uploads
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')
                ->store('letter_settings', 'public');
        }

        if ($request->hasFile('signature_image')) {
            $data['signature_image'] = $request->file('signature_image')
                ->store('letter_settings', 'public');
        }

        if ($request->hasFile('stamp_image')) {
            $data['stamp_image'] = $request->file('stamp_image')
                ->store('letter_settings', 'public');
        }

        // Always create a new record
        LetterSetting::create($data);

        return redirect()->back()
            ->with('success', __('messages.letter_settings_saved_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name_ar' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'authorized_sign_name' => 'required|string|max:255',
            'authorized_sign_title' => 'required|string|max:255',
            'signature_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'stamp_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'company_logo.image' => __('dashboard.company_logo') . ' ' . __('messages.must_be_image'),
            'company_logo.mimes' => __('dashboard.company_logo') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'company_logo.max' => __('dashboard.company_logo') . ' ' . __('messages.max_file_size') . ': 2MB.',
            'signature_image.image' => __('dashboard.signature_image') . ' ' . __('messages.must_be_image'),
            'signature_image.mimes' => __('dashboard.signature_image') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'signature_image.max' => __('dashboard.signature_image') . ' ' . __('messages.max_file_size') . ': 2MB.',
            'stamp_image.image' => __('dashboard.stamp_image') . ' ' . __('messages.must_be_image'),
            'stamp_image.mimes' => __('dashboard.stamp_image') . ' ' . __('messages.allowed_types') . ': JPG, JPEG, PNG, SVG.',
            'stamp_image.max' => __('dashboard.stamp_image') . ' ' . __('messages.max_file_size') . ': 2MB.',
        ]);

        // Find record
        $letter = $this->scopeLetterSettingsForUser(LetterSetting::query(), $request->user())->findOrFail($id);

        // Prepare data
        $data = $request->only([
            'company_name_ar',
            'authorized_sign_name',
            'authorized_sign_title',
        ]);

        /* COMPANY LOGO */
        if ($request->hasFile('company_logo')) {

            // Delete old logo if exists
            if ($letter->company_logo && Storage::disk('public')->exists($letter->company_logo)) {
                Storage::disk('public')->delete($letter->company_logo);
            }

            $data['company_logo'] = $request->file('company_logo')
                ->store('letter_settings', 'public');
        }

        /* SIGNATURE IMAGE */
        if ($request->hasFile('signature_image')) {

            if ($letter->signature_image && Storage::disk('public')->exists($letter->signature_image)) {
                Storage::disk('public')->delete($letter->signature_image);
            }

            $data['signature_image'] = $request->file('signature_image')
                ->store('letter_settings', 'public');
        }

        /* STAMP IMAGE */
        if ($request->hasFile('stamp_image')) {

            if ($letter->stamp_image && Storage::disk('public')->exists($letter->stamp_image)) {
                Storage::disk('public')->delete($letter->stamp_image);
            }

            $data['stamp_image'] = $request->file('stamp_image')
                ->store('letter_settings', 'public');
        }

        // Update record
        $letter->update($data);

        return redirect()->back()
            ->with('success', __('messages.letter_settings_updated_successfully'));
    }

    public function destroy($letterSetting)
    {
        // Find the letter setting record
        $setting = $this->scopeLetterSettingsForUser(LetterSetting::query(), auth()->user())->findOrFail($letterSetting);

        // Delete images if they exist
        if ($setting->company_logo && Storage::disk('public')->exists($setting->company_logo)) {
            Storage::disk('public')->delete($setting->company_logo);
        }

        if ($setting->signature_image && Storage::disk('public')->exists($setting->signature_image)) {
            Storage::disk('public')->delete($setting->signature_image);
        }

        if ($setting->stamp_image && Storage::disk('public')->exists($setting->stamp_image)) {
            Storage::disk('public')->delete($setting->stamp_image);
        }

        // Delete the record
        $setting->delete();

        // Return response
        return redirect()->back()->with('delete', __('messages.letter_settings_deleted_successfully'));
    }

    private function scopeLetterSettingsForUser($query, $user)
    {
        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id() ?: $user?->company_id;
    }

    private function isGlobalSuperAdmin($user): bool
    {
        return ! $this->tenantIdForUser($user)
            && ($user?->hasRole('super_admin') || $user?->role === 'super_admin');
    }
}
