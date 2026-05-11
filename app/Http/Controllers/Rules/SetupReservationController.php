<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\ReservationSetting;
use Illuminate\Http\Request;

class SetupReservationController extends Controller
{
    public function index()
    {
        $settings = ReservationSetting::getSettings();

        return view('admin.setup_reservation.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_view' => 'required|in:list,calendar',
        ]);

        $settings = ReservationSetting::getSettings();

        $settings->default_view = $validated['default_view'];

        $settings->save();

        return back()->with('success', __('messages.setting_updated_successfully'));
    }
}
