<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\NightAuditSetting;
use Illuminate\Http\Request;

class NightAuditController extends Controller
{
    public function index()
    {
        $settings = NightAuditSetting::getSettings();

        return view('admin.night_audit.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = NightAuditSetting::getSettings();

        $settings->is_active = $request->has('is_active');
        $settings->allowance_period = $request->allowance_period ?? 0;
        $settings->cancellation_threshold = $request->cancellation_threshold;

        $settings->save();

        return back()->with('success', __('messages.night_audit_setting_updated_successfully'));
    }
}
