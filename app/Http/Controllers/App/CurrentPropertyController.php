<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Support\PropertyContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentPropertyController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'integer'],
        ]);

        abort_unless($request->user()?->canAccessProperty((int) $validated['property_id']), 403);

        $property = Property::findOrFail((int) $validated['property_id']);

        $request->session()->put('property_id', $property->id);
        app(PropertyContext::class)->setProperty($property);

        return back()->with('success', 'Current branch switched successfully.');
    }
}
