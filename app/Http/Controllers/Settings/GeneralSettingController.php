<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $setting = GeneralSetting::first();

        return view('Admin.Backend.Settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        // Validation
        $request->validate([
            'hrm_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'logo_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'dashboard_background' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Get first (and only) general settings record
        $setting = GeneralSetting::first() ?? new GeneralSetting;

        // Update basic info
        $setting->hrm_name = $request->hrm_name;
        $setting->email = $request->email;
        $setting->phone = $request->phone;

        // Upload logo and delete old logo
        if ($request->hasFile('logo_path')) {
            // Delete old logo file
            if ($setting->logo_path && file_exists(public_path($setting->logo_path))) {
                unlink(public_path($setting->logo_path));
            }

            $file = $request->file('logo_path');
            $filename = 'logo_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/hrm_logos'), $filename);
            $setting->logo_path = 'uploads/hrm_logos/'.$filename;
        }

        // Upload dashboard background and delete old file
        if ($request->hasFile('dashboard_background')) {
            // Delete old background file
            if ($setting->dashboard_background && file_exists(public_path($setting->dashboard_background))) {
                unlink(public_path($setting->dashboard_background));
            }

            $file = $request->file('dashboard_background');
            $filename = 'dashboard_bg_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/dashboard_bg'), $filename);
            $setting->dashboard_background = 'uploads/dashboard_bg/'.$filename;
        }

        // Save settings
        $setting->save();

        // Response
        return response()->json([
            'success' => true,
            'message' => __('messages.general_settings_updated_successfully'),
        ]);
    }
}
