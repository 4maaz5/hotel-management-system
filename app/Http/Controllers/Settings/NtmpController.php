<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\NtmpSetting;
use App\Models\NtmpSubmission;
use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NtmpController extends Controller
{
    public function index()
    {
        if ($redirect = $this->redirectWhenPropertyIsMissing()) {
            return $redirect;
        }

        $setting = NtmpSetting::current();
        $submissions = NtmpSubmission::query()
            ->with(['reservation', 'guest'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.ntmp.index', compact('setting', 'submissions'));
    }

    public function update(Request $request)
    {
        if ($redirect = $this->redirectWhenPropertyIsMissing()) {
            return $redirect;
        }

        $setting = NtmpSetting::current();

        $validated = $request->validate([
            'mode' => [
                'required',
                Rule::in($request->input('driver') === 'fake'
                    ? ['simulation', 'test']
                    : ['simulation', 'test', 'live']),
            ],
            'driver' => 'required|in:fake',
            'provider_name' => 'nullable|string|max:255',
            'endpoint' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:1000',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'branch_reference' => 'nullable|string|max:255',
            'license_reference' => 'nullable|string|max:255',
            'establishment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (($validated['password'] ?? '') === '') {
            unset($validated['password']);
        }

        if (($validated['api_key'] ?? '') === '') {
            unset($validated['api_key']);
        }

        $setting->update([
            'enabled' => $request->boolean('enabled'),
            ...$validated,
            'connection_status' => $request->boolean('enabled')
                ? ($validated['mode'] === 'live' ? 'pending_live_setup' : 'simulation_ready')
                : 'not_connected',
        ]);

        return back()->with('success', 'Saudi NTMP settings updated successfully.');
    }

    public function show(int $submission)
    {
        $query = NtmpSubmission::query()
            ->with(['reservation.unit', 'reservation.property', 'guest'])
            ->whereKey($submission);

        if (! auth()->user()?->isSuperAdmin()) {
            if ($redirect = $this->redirectWhenPropertyIsMissing()) {
                return $redirect;
            }

            $query->where('company_id', app(TenantContext::class)->id())
                ->where('branch_id', app(PropertyContext::class)->branchId());
        } else {
            $query->withoutGlobalScope(TenantScope::class)
                ->withoutGlobalScope(CurrentPropertyScope::class);
        }

        $submission = $query->firstOrFail();

        $submission->load(['reservation.unit', 'reservation.property', 'guest']);

        return view('admin.ntmp.show', compact('submission'));
    }

    private function redirectWhenPropertyIsMissing()
    {
        if (app(PropertyContext::class)->id() && app(PropertyContext::class)->branchId()) {
            return null;
        }

        return redirect()
            ->route('setup-sidebar.property.index')
            ->with('warning', 'Please select or create a branch before configuring Saudi NTMP.');
    }
}
