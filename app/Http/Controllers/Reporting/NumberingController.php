<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Models\ReportSetting;
use Illuminate\Http\Request;

class NumberingController extends Controller
{
    public function index(Request $request)
    {
        $settings = ReportSetting::all();

        $editSetting = null;

        if ($request->has('edit')) {
            $editSetting = ReportSetting::find($request->edit);
        }

        return view('admin.numbering_option.index', compact(
            'settings',
            'editSetting'
        ));
    }

    public function update(Request $request, $id)
    {
        $setting = ReportSetting::findOrFail($id);

        $validated = $request->validate([
            'naming_method' => 'required',
            'prefix' => 'nullable|max:10',
            'current_sequence' => 'required|integer|min:1',
            'reset_yearly' => 'nullable|boolean',
        ]);

        $setting->update($validated);

        return redirect()
            ->route('setup-sidebar.numbering_option.index')
            ->with('success', __('messages.report_setting_updated_successfully'));
    }
}
