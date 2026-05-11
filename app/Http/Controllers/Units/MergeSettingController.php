<?php

namespace App\Http\Controllers\Units;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Unit;
use App\Models\UnitClass;
use App\Models\UnitMerge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MergeSettingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('unit_number');

        $unitMerges = UnitMerge::with([
            'unitA.unitClass',
            'unitA.unitType',
            'unitB.unitClass',
            'unitB.unitType',
        ])
            ->when($search, function ($query, $search) {
                $query->where(function ($mergeQuery) use ($search) {
                    $mergeQuery->whereHas('unitA', function ($q) use ($search) {
                        $q->where('unit_number', 'like', '%'.$search.'%');
                    })->orWhereHas('unitB', function ($q) use ($search) {
                        $q->where('unit_number', 'like', '%'.$search.'%');
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.merge_settings.index', compact('unitMerges'));
    }

    public function create()
    {
        $blocks = Block::where('is_active', true)->get();
        $units = UnitClass::where('is_active', true)->get();

        return view('admin.merge_settings.create', compact('blocks', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'floor_id' => 'required|exists:floors,id',
            'unit_class_id' => 'required|exists:unit_classes,id',
            'unit_a_id' => 'required|exists:units,id|different:unit_b_id',
            'unit_b_id' => 'required|exists:units,id',
        ]);

        $unitAId = min($request->unit_a_id, $request->unit_b_id);
        $unitBId = max($request->unit_a_id, $request->unit_b_id);

        $unitA = Unit::findOrFail($unitAId);
        $unitB = Unit::findOrFail($unitBId);

        if ($unitA->floor_id !== $unitB->floor_id ||
            $unitA->block_id !== $unitB->block_id) {

            return back()->with('danger', __('messages.units_must_be_in_the_same_block_and_floor'));
        }

        // Prevent already merged
        $exists = UnitMerge::where(function ($q) use ($unitAId, $unitBId) {
            $q->where('unit_a_id', $unitAId)
                ->orWhere('unit_b_id', $unitAId)
                ->orWhere('unit_a_id', $unitBId)
                ->orWhere('unit_b_id', $unitBId);
        })->exists();

        if ($exists) {
            return back()->with('danger', __('messages.one_of_the_units_already_merged'));
        }

        $mergeCode = $unitA->unit_number.'-'.$unitB->unit_number;

        UnitMerge::create([
            'block_id' => $request->block_id,
            'floor_id' => $request->floor_id,
            'unit_class_id' => $request->unit_class_id,
            'merge_code' => $mergeCode,
            'unit_a_id' => $unitAId,
            'unit_b_id' => $unitBId,
            'is_active' => true,
        ]);

        return redirect()
            ->route('setup-sidebar.merge_setting.index')
            ->with('success', __('messages.units_merged_setting_added'));
    }

    public function destroy($id)
    {
        $unitMerge = UnitMerge::findOrFail($id);

        $unitMerge->delete();

        return redirect()
            ->route('setup-sidebar.merge_setting.index')
            ->with('danger', __('messages.unit_merge_setting_deleted_successfully'));
    }

    public function getFloors($blockId)
    {
        $floors = Floor::where('block_id', $blockId)
            ->where('is_active', true)
            ->get();

        return response()->json($floors);
    }

    public function getUnits(Request $request)
    {
        $query = Unit::query();

        if ($request->has('block_id') && $request->block_id) {
            $query->where('block_id', $request->block_id);
        }

        if ($request->has('floor_id') && $request->floor_id) {
            $query->where('floor_id', $request->floor_id);
        }

        if ($request->has('unit_class_id') && $request->unit_class_id) {
            $query->where('unit_class_id', $request->unit_class_id);
        }

        $query->where('can_be_merged', true);

        $query->whereNotIn('id', function ($subQuery) {
            $subQuery->select('unit_a_id')->from('unit_merges')
                ->union(
                    UnitMerge::query()->select('unit_b_id')
                );
        });

        $units = $query->get(['id', 'unit_number']);

        return response()->json($units);
    }
}
