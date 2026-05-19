<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Block;
use App\Models\Floor;
use App\Models\HallType;
use App\Models\Unit;
use App\Models\UnitClass;
use App\Models\UnitTypeCustomization;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query();

        // Filter: Block
        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Filter: Floor
        if ($request->filled('floor_id')) {
            $query->where('floor_id', $request->floor_id);
        }

        // Filter: Unit Number
        if ($request->filled('unit_number')) {
            $query->where('unit_number', 'like', '%'.$request->unit_number.'%');
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Filter: Unit Class
        if ($request->filled('unit_class_id')) {
            $query->where('unit_class_id', $request->unit_class_id);
        }

        // Filter: Unit Type
        if ($request->filled('unit_type_id')) {
            $query->where('unit_type_id', $request->unit_type_id);
        }

        $units = $query->latest()->paginate(12);

        // Needed for dropdowns
        $blocks = Block::all();
        $floors = Floor::all();
        $unitClasses = UnitClass::all();
        $unitTypes = UnitTypeCustomization::all();

        return view('admin.unit.index', compact(
            'units',
            'blocks',
            'floors',
            'unitClasses',
            'unitTypes'
        ));
    }

    public function create()
    {
        return view('admin.unit.create', [
            'unitTypes' => UnitTypeCustomization::all(),
            'amenities' => Amenity::where('is_active', 1)->get(),
            'unitClasses' => UnitClass::where('is_active', 1)->get(),
            'hallTypes' => HallType::where('is_active', 1)->get(),
            'blocks' => Block::all(),
            'floors' => Floor::all(),
        ]);
    }

    public function store(Request $request)
    {
        [$companyId, $branchId] = $this->currentTenantAndBranch($request);

        abort_unless($branchId, 422, 'Please select or create a branch first.');

        $validated = $request->validate([
            'unit_number' => [
                'required',
                'string',
                $this->uniqueUnitNumberRule($companyId, $branchId),
            ],
            'unit_class_id' => 'required|exists:unit_classes,id',
            'unit_type_id' => 'required|exists:unit_type_customizations,id',
            'block_id' => [
                'required',
                Rule::exists('blocks', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'floor_id' => [
                'required',
                Rule::exists('floors', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'hall_type_id' => 'nullable|exists:hall_types,id',
            'phone_extension' => 'nullable|string|max:20',
            'number_of_toilets' => 'required|integer|min:1',
            'kitchen_type' => 'nullable|string|max:255',
            'unit_area' => 'nullable|numeric|min:0',
            'number_of_single_beds' => 'required|integer|min:0',
            'number_of_double_beds' => 'required|integer|min:0',
            'base_occupancy' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $unit = Unit::create([
            'unit_number' => $validated['unit_number'],
            'unit_class_id' => $validated['unit_class_id'],
            'unit_type_id' => $validated['unit_type_id'],
            'block_id' => $validated['block_id'],
            'floor_id' => $validated['floor_id'],
            'hall_type_id' => $validated['hall_type_id'] ?? null,
            'phone_extension' => $validated['phone_extension'] ?? null,
            'number_of_toilets' => $validated['number_of_toilets'],
            'kitchen_type' => $validated['kitchen_type'] ?? null,
            'unit_area' => $validated['unit_area'] ?? null,
            'number_of_single_beds' => $validated['number_of_single_beds'],
            'number_of_double_beds' => $validated['number_of_double_beds'],
            'base_occupancy' => $validated['base_occupancy'],
            'description' => $validated['description'] ?? null,
            'can_be_merged' => $request->boolean('can_be_merged'),
            'is_active' => true,
        ]);

        if (! empty($validated['amenities'])) {
            $unit->amenities()->sync($validated['amenities']);
        }

        return redirect()
            ->route('setup-sidebar.unit.index')
            ->with('success', __('messages.new_unit_created_successfully'));
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);

        return view('admin.unit.edit', [
            'unit' => $unit,
            'unitTypes' => UnitTypeCustomization::all(),
            'amenities' => Amenity::where('is_active', 1)->get(),
            'unitClasses' => UnitClass::where('is_active', 1)->get(),
            'hallTypes' => HallType::where('is_active', 1)->get(),
            'blocks' => Block::all(),
            'floors' => Floor::where('block_id', $unit->block_id)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        [$companyId, $branchId] = $this->currentTenantAndBranch($request, $unit);

        $validated = $request->validate([
            'unit_number' => [
                'required',
                'string',
                $this->uniqueUnitNumberRule($companyId, $branchId, $unit->id),
            ],
            'unit_class_id' => 'required|exists:unit_classes,id',
            'unit_type_id' => 'required|exists:unit_type_customizations,id',
            'block_id' => [
                'required',
                Rule::exists('blocks', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'floor_id' => [
                'required',
                Rule::exists('floors', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'hall_type_id' => 'nullable|exists:hall_types,id',
            'phone_extension' => 'nullable|string|max:20',
            'number_of_toilets' => 'required|integer|min:1',
            'kitchen_type' => 'nullable|string|max:255',
            'unit_area' => 'nullable|numeric|min:0',
            'number_of_single_beds' => 'required|integer|min:0',
            'number_of_double_beds' => 'required|integer|min:0',
            'base_occupancy' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $unit->update([
            'unit_number' => $validated['unit_number'],
            'unit_class_id' => $validated['unit_class_id'],
            'unit_type_id' => $validated['unit_type_id'],
            'block_id' => $validated['block_id'],
            'floor_id' => $validated['floor_id'],
            'hall_type_id' => $validated['hall_type_id'] ?? null,
            'phone_extension' => $validated['phone_extension'] ?? null,
            'number_of_toilets' => $validated['number_of_toilets'],
            'kitchen_type' => $validated['kitchen_type'] ?? null,
            'unit_area' => $validated['unit_area'] ?? null,
            'number_of_single_beds' => $validated['number_of_single_beds'],
            'number_of_double_beds' => $validated['number_of_double_beds'],
            'base_occupancy' => $validated['base_occupancy'],
            'description' => $validated['description'] ?? null,
            'can_be_merged' => $request->boolean('can_be_merged'),

            'is_active' => $request->has('status'),
        ]);

        $unit->amenities()->sync($validated['amenities'] ?? []);

        return redirect()
            ->route('setup-sidebar.unit.index')
            ->with('success', __('messages.unit_updated_successfully'));
    }

    public function view($id)
    {
        $unit = Unit::findOrFail($id);

        return view('admin.unit.view', [
            'unit' => $unit,
            'unitTypes' => UnitTypeCustomization::all(),
            'amenities' => Amenity::where('is_active', 1)->get(),
            'unitClasses' => UnitClass::where('is_active', 1)->get(),
            'hallTypes' => HallType::where('is_active', 1)->get(),
            'blocks' => Block::all(),
            'floors' => Floor::where('block_id', $unit->block_id)->get(),
        ]);
    }

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);

        $unit->amenities()->detach();

        $unit->delete();

        return redirect()
            ->route('setup-sidebar.unit.index')
            ->with('danger', __('messages.unit_deleted_successfully'));
    }

    private function currentTenantAndBranch(Request $request, ?Unit $unit = null): array
    {
        $propertyContext = app(PropertyContext::class);
        $tenantContext = app(TenantContext::class);

        $branchId = $propertyContext->branchId() ?: $unit?->branch_id;
        $companyId = $tenantContext->id() ?: $unit?->company_id ?: $request->user()?->company_id;

        return [$companyId, $branchId];
    }

    private function uniqueUnitNumberRule(?int $companyId, ?int $branchId, ?int $ignoreId = null): \Illuminate\Validation\Rules\Unique
    {
        $rule = Rule::unique('units', 'unit_number')
            ->where(fn ($query) => $query
                ->where('company_id', $companyId)
                ->where('branch_id', $branchId));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }
}
