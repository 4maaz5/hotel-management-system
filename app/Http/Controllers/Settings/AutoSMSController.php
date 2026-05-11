<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Models\SmsTemplate;
use App\Models\SmsUser;
use App\Models\SmsUserSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AutoSMSController extends Controller
{
    public function index()
    {
        $setting = SmsSetting::first();
        $templates = SmsTemplate::where('recipient', 'guest')->get();
        $appendedUsers = SmsUser::with(['user', 'smsTypes'])->get();
        $availableUsers = User::whereNotIn(
            'id',
            $appendedUsers->pluck('user_id')
        )->get();

        return view('admin.auto_sms.index', compact(
            'setting',
            'templates',
            'appendedUsers',
            'availableUsers'
        ));
    }

    public function update(Request $request)
    {
        $setting = SmsSetting::firstOrCreate([]);

        $setting->update([
            'property_name' => $request->property_name,
            'default_language' => $request->default_language,
            'show_property_name' => $request->has('show_property_name'),
        ]);

        if ($request->guest) {
            foreach ($request->guest as $key => $data) {

                SmsTemplate::updateOrCreate(
                    ['type' => $key, 'recipient' => 'guest'],
                    [
                        'enabled' => isset($data['enabled']),
                        'message' => $data['message'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', __('messages.sms_settings_updated_successfully'));
    }

    public function appendUser(Request $request)
    {
        $request->validate(['user_id' => 'required']);
        SmsUser::create([
            'user_id' => $request->user_id,
        ]);

        return back()->with('success', __('messages.user_appended_successfully'));
    }

    public function delete($id)
    {
        $smsDelete = SmsUser::findOrfail($id);
        $smsDelete->delete();

        return redirect()->back()->with('danger', __('messages.SMS_user_deleted_successfully'));
    }

    public function getUserTypes($userId)
    {
        $selected = SmsUserSetting::where('user_id', $userId)
            ->pluck('sms_template_id');

        return response()->json($selected);
    }

    public function saveUserTypes(Request $request)
    {
        if (! $request->user_id) {
            return redirect()->back()->with('danger', __('messages.user_id_cannot_be_null'));
        }

        SmsUserSetting::where('user_id', $request->user_id)->delete();

        if ($request->has('types') && count($request->types) > 0) {

            foreach ($request->types as $templateId) {

                SmsUserSetting::create([
                    'user_id' => $request->user_id,
                    'sms_template_id' => $templateId,
                    'enabled' => true,
                ]);

            }
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.sms_user_setting_added_successfully'),
        ]);
    }
}
