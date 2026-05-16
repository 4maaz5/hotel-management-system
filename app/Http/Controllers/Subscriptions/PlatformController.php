<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ThirdPartyPlatform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $platforms = $this->scopePlatformsForUser(ThirdPartyPlatform::with('company'), $user)
            ->latest()
            ->get();

        return view('Admin.Backend.Subscriptions.index', compact('companies', 'platforms'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $validated = $request->validate([
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        //  Create platform
        ThirdPartyPlatform::create([
            'company_id' => $this->isSuperAdmin($request->user()) ? $validated['company_id'] : $request->user()->company_id,
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        //  Redirect with success message
        return redirect()
            ->back()
            ->with('success', __('messages.third_party_platform_added_successfully'));
    }

    public function update(Request $request, ThirdPartyPlatform $platform)
    {
        $platform = $this->scopePlatformsForUser(ThirdPartyPlatform::query(), $request->user())
            ->findOrFail($platform->id);

        //  Validate incoming request
        $validated = $request->validate([
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        //  Update the platform
        $platform->update([
            'company_id' => $this->isSuperAdmin($request->user()) ? $validated['company_id'] : $request->user()->company_id,
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        //  Redirect back with success message
        return redirect()
            ->back()
            ->with('success', __('messages.third_party_platform_updated_successfully'));
    }

    public function destroy(ThirdPartyPlatform $platform)
    {
        $platform = $this->scopePlatformsForUser(ThirdPartyPlatform::query(), auth()->user())
            ->findOrFail($platform->id);

        $platform->delete();

        return redirect()->back()->with('delete', __('messages.third_party_platform_deleted_successfully'));
    }

    private function scopePlatformsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}
