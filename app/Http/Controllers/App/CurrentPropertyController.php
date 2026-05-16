<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Support\PropertyBranchManager;
use App\Support\PropertyContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentPropertyController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_property_id' => ['required_without:property_id', 'integer'],
            'property_id' => ['nullable', 'integer'],
        ]);

        $propertyId = (int) ($validated['current_property_id'] ?? $validated['property_id']);

        abort_unless($request->user()?->canAccessProperty($propertyId), 403);

        $property = $request->user()
            ->accessiblePropertiesQuery()
            ->findOrFail($propertyId);
        $property = app(PropertyBranchManager::class)->ensureBranch($property, $request->user());

        $request->session()->put('current_property_id', $property->id);
        $request->session()->forget('property_id');
        $request->session()->put('branch_id', $property->branch_id);
        app(PropertyContext::class)->setProperty($property);

        return back()->with('success', 'Current branch switched successfully.');
    }
}
