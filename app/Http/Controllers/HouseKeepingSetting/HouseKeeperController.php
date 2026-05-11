<?php

namespace App\Http\Controllers\HouseKeepingSetting;

use App\Http\Controllers\Controller;
use App\Models\Housekeeper;
use App\Models\User;
use Illuminate\Http\Request;

class HouseKeeperController extends Controller
{
    public function index(Request $request)
    {
        $query = Housekeeper::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $housekeepers = $query->orderBy('id', 'desc')->paginate(10);
        $existingHousekeeperUserIds = Housekeeper::pluck('user_id')->toArray();
        $users = User::where('status', 'active')->whereNotIn('id', $existingHousekeeperUserIds)->get();

        return view('admin.housekeeper_setting.index', compact('housekeepers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:housekeepers,user_id',
        ]);

        Housekeeper::create([
            'user_id' => $request->user_id,
            'is_active' => true,
            'sms_notification' => $request->has('sms_notification'),
        ]);

        return back()->with('success', __('messages.setting_updated_successfully'));
    }

    public function update(Request $request, $id)
    {
        $housekeeper = Housekeeper::findOrFail($id);

        $housekeeper->update([
            'is_active' => $request->has('is_active'),
            'sms_notification' => $request->has('sms_notification'),
        ]);

        return back()->with('success', __('messages.setting_updated_successfully'));
    }

    public function destroy($id)
    {
        $housekeeper = Housekeeper::findOrFail($id);
        $housekeeper->delete();

        return back()->with('success', __('messages.setting_updated_successfully'));
    }
}
