<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ThirdPartyPlatform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        $platforms = ThirdPartyPlatform::with('company')
            ->latest()
            ->get();

        return view('Admin.Backend.Subscriptions.index', compact('companies', 'platforms'));
    }

    public function store(Request $request)
    {
        //  Validate request
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        //  Create platform
        ThirdPartyPlatform::create([
            'company_id' => $validated['company_id'],
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
        //  Validate incoming request
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        //  Update the platform
        $platform->update([
            'company_id' => $validated['company_id'],
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
        $platform->delete();

        return redirect()->back()->with('delete', __('messages.third_party_platform_deleted_successfully'));
    }
}
