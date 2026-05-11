<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ReservationSourceMaster;
use App\Models\ReservationSourceSetting;
use Illuminate\Http\Request;

class ReservationSourceController extends Controller
{
    public function index()
    {
        $sources = ReservationSourceMaster::all();
        $settings = ReservationSourceSetting::with('masterSource')->get();

        return view('admin.reservation_source.index', compact('sources', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_channel_id' => 'required|exists:reservation_source_masters,id',
            'report_name' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'tax_mode' => 'required|in:auto,manual',
            'tax_calculation_type' => 'nullable|in:inclusive,exclusive',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        ReservationSourceSetting::updateOrCreate(
            [
                'master_source_id' => $request->master_channel_id,
            ],
            [
                'status' => $request->status ?? 0,
                'report_name' => $request->report_name,
                'url' => $request->url,
                'commission_rate' => $request->commission_rate,
                'tax_mode' => $request->tax_mode,
                'tax_calculation_type' => $request->tax_mode === 'manual'
                        ? $request->tax_calculation_type
                        : null,
                'description' => $request->description,
            ]
        );

        return back()->with(
            'success',
            __('messages.reservation_source_added_successfully')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'report_name' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'tax_mode' => 'required|in:auto,manual',
            'tax_calculation_type' => 'nullable|in:inclusive,exclusive',
            'description' => 'nullable|string',
        ]);

        $setting = ReservationSourceSetting::findOrFail($id);

        $setting->update([
            'status' => $request->has('status') ? 1 : 0,
            'report_name' => $request->report_name,
            'url' => $request->url,
            'commission_rate' => $request->commission_rate,
            'tax_mode' => $request->tax_mode,
            'tax_calculation_type' => $request->tax_mode === 'manual'
                    ? $request->tax_calculation_type
                    : null,
            'description' => $request->description,
        ]);

        return back()->with(
            'success',
            __('messages.reservation_source_updated_successfully')
        );
    }

    public function delete($id)
    {
        $source = ReservationSourceSetting::findOrfail($id);
        $source->delete();

        return redirect()->back()->with('danger', __('messages.reservation_source_deleted_successfully'));
    }
}
