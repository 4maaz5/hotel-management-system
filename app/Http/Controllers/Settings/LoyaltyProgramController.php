<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyAutoSetting;
use App\Models\LoyaltySetting;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoyaltyProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = LoyaltySetting::with('guestClass');

        if ($request->filled('criteria')) {
            $query->where('criteria', $request->criteria);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', 'like', '%'.$request->created_by.'%');
        }

        $loyaltySettings = $query->latest()->get();

        return view('admin.loyalty_program.index',
            compact('loyaltySettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criteria' => 'required',
            'threshold_value' => 'required|integer',
            'upgrade_to_class_id' => [
                'required',
                Rule::exists('guest_classes', 'id')
                    ->where(fn ($query) => $query->where('company_id', $this->currentCompanyId($request))),
            ],
        ]);

        LoyaltySetting::create([
            'criteria' => $request->criteria,
            'threshold_value' => $request->threshold_value,
            'upgrade_to_class_id' => $request->upgrade_to_class_id,
            'created_by' => auth()->user()->name ?? null,
        ]);

        return back()->with(
            'success',
            __('messages.loyalty_setting_created_successfully')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'criteria' => 'required',
            'threshold_value' => 'required|integer',
            'upgrade_to_class_id' => [
                'required',
                Rule::exists('guest_classes', 'id')
                    ->where(fn ($query) => $query->where('company_id', $this->currentCompanyId($request))),
            ],
        ]);

        $setting = LoyaltySetting::findOrFail($id);

        $setting->update([
            'criteria' => $request->criteria,
            'threshold_value' => $request->threshold_value,
            'upgrade_to_class_id' => $request->upgrade_to_class_id,
            'is_active' => $request->is_active ?? 0,
        ]);

        return back()->with(
            'success',
            __('messages.loyalty_setting_updated_successfully')
        );
    }

    public function delete($id)
    {
        $loyaltySetting = LoyaltySetting::findOrfail($id);
        $loyaltySetting->delete();

        return redirect()->back()->with('danger', __('messages.loyalty_setting_deleted_successfully'));
    }

    public function toggleAutoUpgrade(Request $request)
    {
        $setting = LoyaltyAutoSetting::first();

        if (! $setting) {
            $setting = LoyaltyAutoSetting::create([
                'auto_loyalty_upgrade' => $request->auto_loyalty_upgrade ? 1 : 0,
            ]);
        } else {

            $setting->update([
                'auto_loyalty_upgrade' => $request->auto_loyalty_upgrade ? 1 : 0,
            ]);
        }

        return back()->with(
            'success',
            __('messages.loyality_auto_setting_updated_successfully')
        );
    }

    private function currentCompanyId(Request $request): ?int
    {
        return app(TenantContext::class)->id() ?: $request->user()?->company_id;
    }
}
