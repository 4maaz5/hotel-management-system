<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\DateTimeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DateController extends Controller
{
    public function index()
    {
        $dateTimeSetting = DateTimeSetting::first();

        return view('admin.date_time.index', compact('dateTimeSetting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'date_format' => 'required',
            'time_format' => 'required|in:12,24',
        ]);

        DateTimeSetting::updateOrCreate(
            ['id' => 1],
            [
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
            ]
        );

        Cache::forget('system_settings');

        return back()->with('success', __('messages.date_and_time_setting_updated_successfully'));
    }
}
