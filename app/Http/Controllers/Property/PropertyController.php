<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Region;
use App\Support\PropertyBranchManager;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->filled('name')) {
            $query->where(function ($propertyQuery) use ($request) {
                $propertyQuery->where('property_name_en', 'like', '%'.$request->name.'%')
                    ->orWhere('property_code', 'like', '%'.$request->name.'%');
            });
        }

        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        if ($request->filled('account_version')) {
            $query->where('account_version', strtoupper($request->account_version));
        }

        $properties = $query->with(['country', 'city', 'district'])->get();

        return view('admin.property.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.property.create', [
            'tenant' => auth()->user()?->tenant,
            'propertyTypes' => PropertyType::where('is_active', true)->get(),
            'countries' => Country::all(),
            'regions' => Region::all(),
            'cities' => City::all(),
            'districts' => District::all(),
        ]);
    }

    // Store new property
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_name_en' => 'required|string|max:255',
            'property_name_ar' => 'required|string|max:255',
            'report_name_en' => 'required|string|max:255',
            'report_name_ar' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'account_version' => 'nullable|in:BASIC,PREMIUM,ENTERPRISE',
            'logo' => 'nullable|image|max:2048',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            // 'street_id' => 'nullable|exists:locations,id',
            'address_en' => 'required|string',
            'address_ar' => 'required|string',
            'building_no' => 'required|string|max:50',
            'secondary_no' => 'nullable|string|max:50',
            'po_box' => 'nullable|string|max:20',
            'postal_code' => 'required|string|max:20',
            'short_address' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'time_zone' => 'required|string',
            'phone' => 'required|string|max:50',
            'mobile' => 'required|string|max:50',
            'fax_number' => 'nullable|string|max:50',
            'hot_line' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:500',
            'admin_number' => 'nullable|string|max:50',
            'active_units_count' => 'required|integer|min:0',
            'max_units_count' => 'required|integer|min:0',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('property/logo', 'public');
            $validated['logo_url'] = $path;
        }
        $validated['owner_user_id'] = auth()->id();
        $validated['account_expiry_date'] = auth()->user()?->tenant?->end_date;
        $validated['property_code'] = $this->generatePropertyCode($validated['property_name_en']);

        // Create property
        $property = DB::transaction(function () use ($request, $validated) {
            $property = Property::create($validated);

            return app(PropertyBranchManager::class)->ensureBranch($property, $request->user());
        });

        app(UserActivityLogger::class)->log(
            'property_setup',
            'created',
            $property,
            "Created property {$property->property_name_en}",
            [],
            $this->propertyActivityData($property),
            ['area' => 'property']
        );

        return redirect()->route('setup-sidebar.property.index')
            ->with('success', __('messages.property_created_successfully'));
    }

    public function edit(Property $property)
    {
        return view('admin.property.edit', [
            'property' => $property,
            'tenant' => auth()->user()?->tenant,
            'propertyTypes' => PropertyType::where('is_active', true)->get(),
            'countries' => Country::all(),
            'regions' => Region::all(),
            'cities' => City::all(),
            'districts' => District::all(),
        ]);
    }

    public function show(Property $property)
    {
        return view('admin.property.view', [
            'property' => $property,
            'tenant' => auth()->user()?->tenant,
            'propertyTypes' => PropertyType::where('is_active', true)->get(),
            'countries' => Country::all(),
            'regions' => Region::all(),
            'cities' => City::all(),
            'districts' => District::all(),
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $before = $this->propertyActivityData($property);
        $validated = $request->validate([
            'property_name_en' => 'required|string|max:255',
            'property_name_ar' => 'required|string|max:255',
            'report_name_en' => 'required|string|max:255',
            'report_name_ar' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
            'property_code' => 'required|string|max:50|unique:properties,property_code,'.$property->id,
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'account_version' => 'nullable|in:BASIC,PREMIUM,ENTERPRISE',
            'account_expiry_date' => 'nullable|date',
            'logo' => 'nullable|image|max:2048',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            // 'street_id' => 'nullable|exists:locations,id',
            'address_en' => 'required|string',
            'address_ar' => 'required|string',
            'building_no' => 'required|string|max:50',
            'secondary_no' => 'nullable|string|max:50',
            'po_box' => 'nullable|string|max:20',
            'postal_code' => 'required|string|max:20',
            'short_address' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'time_zone' => 'required|string',
            'phone' => 'required|string|max:50',
            'mobile' => 'required|string|max:50',
            'fax_number' => 'nullable|string|max:50',
            'hot_line' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:500',
            'admin_number' => 'nullable|string|max:50',
            'active_units_count' => 'required|integer|min:0',
            'max_units_count' => 'required|integer|min:0',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($property->logo_url) {
                Storage::disk('public')->delete($property->logo_url);
            }
            $path = $request->file('logo')->store('property/logo', 'public');
            $validated['logo_url'] = $path;
        } elseif ($request->has('existing_logo')) {
            $validated['logo_url'] = $request->existing_logo;
        }

        $property->update($validated);

        app(UserActivityLogger::class)->log(
            'property_setup',
            'updated',
            $property,
            "Updated property {$property->property_name_en}",
            $before,
            $this->propertyActivityData($property->fresh()),
            ['area' => 'property']
        );

        return redirect()->route('setup-sidebar.property.index')
            ->with('success', __('messages.property_updated_successfully'));
    }

    protected function generatePropertyCode(string $propertyName): string
    {
        $prefix = (string) Str::of($propertyName)
            ->ascii()
            ->upper()
            ->replaceMatches('/[^A-Z0-9]+/', '-')
            ->trim('-')
            ->substr(0, 12);

        $prefix = $prefix !== '' ? $prefix : 'PROPERTY';

        do {
            $code = $prefix.'-'.Str::upper(Str::random(6));
        } while (Property::withoutGlobalScopes()->where('property_code', $code)->exists());

        return $code;
    }

    protected function propertyActivityData(Property $property): array
    {
        return [
            'property_code' => $property->property_code,
            'property_name_en' => $property->property_name_en,
            'status' => $property->status,
            'account_version' => $property->account_version,
            'country_id' => $property->country_id,
            'city_id' => $property->city_id,
            'district_id' => $property->district_id,
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'phone' => $property->phone,
            'email' => $property->email,
            'active_units_count' => $property->active_units_count,
            'max_units_count' => $property->max_units_count,
        ];
    }
}
