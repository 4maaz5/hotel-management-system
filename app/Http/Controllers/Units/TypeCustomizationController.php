<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\UnitType;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeCustomizationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TypeCustomizationController extends Controller
{
    public function index()
    {
        $typeCustomizations = UnitTypeCustomization::with('unitType')
            ->withCount('images')
            ->orderBy('website_sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.UnitTypeCustomization.index', compact('typeCustomizations'));
    }

    public function create()
    {
        $unitTypes = UnitType::where('is_active', true)->get();

        return view('admin.UnitTypeCustomization.create', compact('unitTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_type_id' => 'required|exists:unit_types,id',
            'name' => 'required|string|max:255',
            'website_name_en' => 'nullable|string|max:255',
            'website_name_ar' => 'nullable|string|max:255',
            'unit_area' => 'nullable|numeric|min:0',
            'single_beds' => 'nullable|integer|min:0',
            'double_beds' => 'nullable|integer|min:0',
            'base_occupancy' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'website_summary_en' => 'nullable|string|max:500',
            'website_summary_ar' => 'nullable|string|max:500',
            'website_description_en' => 'nullable|string',
            'website_description_ar' => 'nullable|string',
            'website_slug' => 'nullable|string|max:255|unique:unit_type_customizations,website_slug',
            'seo_title_en' => 'nullable|string|max:255',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_description_en' => 'nullable|string|max:500',
            'seo_description_ar' => 'nullable|string|max:500',
            'is_published_online' => 'nullable|boolean',
            'website_sort_order' => 'nullable|integer|min:0',

            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $websiteSlug = $this->resolveWebsiteSlug(
            $validated['website_slug'] ?? null,
            $validated['website_name_en'] ?? $validated['name'],
        );

        $customization = UnitTypeCustomization::create([
            'unit_type_id' => $validated['unit_type_id'],
            'name' => $validated['name'],
            'website_name_en' => $validated['website_name_en'] ?? null,
            'website_name_ar' => $validated['website_name_ar'] ?? null,
            'unit_area' => $validated['unit_area'] ?? null,
            'single_beds' => $validated['single_beds'] ?? 0,
            'double_beds' => $validated['double_beds'] ?? 0,
            'base_occupancy' => $validated['base_occupancy'],
            'description' => $validated['description'] ?? null,
            'website_summary_en' => $validated['website_summary_en'] ?? null,
            'website_summary_ar' => $validated['website_summary_ar'] ?? null,
            'website_description_en' => $validated['website_description_en'] ?? null,
            'website_description_ar' => $validated['website_description_ar'] ?? null,
            'website_slug' => $websiteSlug !== '' ? $websiteSlug : null,
            'seo_title_en' => $validated['seo_title_en'] ?? null,
            'seo_title_ar' => $validated['seo_title_ar'] ?? null,
            'seo_description_en' => $validated['seo_description_en'] ?? null,
            'seo_description_ar' => $validated['seo_description_ar'] ?? null,
            'is_published_online' => $request->boolean('is_published_online', true),
            'website_sort_order' => $validated['website_sort_order'] ?? 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                $path = $image->store(
                    'unit-type-customizations',
                    'public'
                );

                UnitTypeCustomizationImage::create([
                    'type_customization_id' => $customization->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

        }

        return redirect()
            ->route('setup-sidebar.typeCustomization.index')
            ->with('success', __('messages.new_unit_created_successfully'));
    }

    public function edit($id)
    {
        $typeCustomization = UnitTypeCustomization::with('images')->findOrFail($id);
        $unitTypes = UnitType::where('is_active', true)->get();

        return view('admin.UnitTypeCustomization.edit', compact('typeCustomization', 'unitTypes'));
    }

    public function update(Request $request, $id)
    {
        $typeCustomization = UnitTypeCustomization::with('images')->findOrFail($id);

        $validated = $request->validate([
            'unit_type_id' => 'required|exists:unit_types,id',
            'name' => 'required|string|max:255',
            'website_name_en' => 'nullable|string|max:255',
            'website_name_ar' => 'nullable|string|max:255',
            'unit_area' => 'nullable|numeric|min:0',
            'single_beds' => 'nullable|integer|min:0',
            'double_beds' => 'nullable|integer|min:0',
            'base_occupancy' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'website_summary_en' => 'nullable|string|max:500',
            'website_summary_ar' => 'nullable|string|max:500',
            'website_description_en' => 'nullable|string',
            'website_description_ar' => 'nullable|string',
            'website_slug' => 'nullable|string|max:255|unique:unit_type_customizations,website_slug,'.$typeCustomization->id,
            'seo_title_en' => 'nullable|string|max:255',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_description_en' => 'nullable|string|max:500',
            'seo_description_ar' => 'nullable|string|max:500',
            'is_published_online' => 'nullable|boolean',
            'website_sort_order' => 'nullable|integer|min:0',

            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|exists:unit_type_customization_images,id',
        ]);

        $websiteSlug = $this->resolveWebsiteSlug(
            $validated['website_slug'] ?? null,
            $validated['website_name_en'] ?? $validated['name'],
            $typeCustomization->id
        );

        $typeCustomization->update([
            'unit_type_id' => $validated['unit_type_id'],
            'name' => $validated['name'],
            'website_name_en' => $validated['website_name_en'] ?? null,
            'website_name_ar' => $validated['website_name_ar'] ?? null,
            'unit_area' => $validated['unit_area'] ?? null,
            'single_beds' => $validated['single_beds'] ?? 0,
            'double_beds' => $validated['double_beds'] ?? 0,
            'base_occupancy' => $validated['base_occupancy'],
            'description' => $validated['description'] ?? null,
            'website_summary_en' => $validated['website_summary_en'] ?? null,
            'website_summary_ar' => $validated['website_summary_ar'] ?? null,
            'website_description_en' => $validated['website_description_en'] ?? null,
            'website_description_ar' => $validated['website_description_ar'] ?? null,
            'website_slug' => $websiteSlug !== '' ? $websiteSlug : null,
            'seo_title_en' => $validated['seo_title_en'] ?? null,
            'seo_title_ar' => $validated['seo_title_ar'] ?? null,
            'seo_description_en' => $validated['seo_description_en'] ?? null,
            'seo_description_ar' => $validated['seo_description_ar'] ?? null,
            'is_published_online' => $request->boolean('is_published_online', true),
            'website_sort_order' => $validated['website_sort_order'] ?? 0,
        ]);

        if (! empty($validated['remove_images'])) {
            $imagesToRemove = UnitTypeCustomizationImage::whereIn('id', $validated['remove_images'])
                ->where('type_customization_id', $typeCustomization->id)
                ->get();

            foreach ($imagesToRemove as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('unit-type-customizations', 'public');

                UnitTypeCustomizationImage::create([
                    'type_customization_id' => $typeCustomization->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $typeCustomization->images()->count() + $index,
                ]);
            }
        }

        if (! $typeCustomization->images()->where('is_primary', true)->exists()) {
            $firstImage = $typeCustomization->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return redirect()
            ->route('setup-sidebar.typeCustomization.index')
            ->with('success', __('messages.unit_updated_successfully'));
    }

    public function delete($id)
    {
        $typeCustomization = UnitTypeCustomization::with('images')->findOrFail($id);

        foreach ($typeCustomization->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $typeCustomization->delete();

        return redirect()
            ->route('setup-sidebar.typeCustomization.index')
            ->with('danger', __('messages.unit_deleted_successfully'));
    }

    private function resolveWebsiteSlug(?string $providedSlug, string $fallbackName, ?int $ignoreId = null): ?string
    {
        $baseSlug = Str::slug($providedSlug ?: $fallbackName);

        if ($baseSlug === '') {
            return null;
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            UnitTypeCustomization::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('website_slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
