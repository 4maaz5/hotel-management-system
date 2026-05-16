<?php

namespace App\Http\Controllers\BlocksAndFloors;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Floor;
use App\Support\PropertyContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FloorsController extends Controller
{
    public function index(Request $request)
    {
        $blocks = Block::all();
        $query = Floor::query();

        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('order')) {
            $query->where('order', $request->order);
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        $floors = $query->paginate(10)->withQueryString();

        $block = app(PropertyContext::class)->property()?->blocks()->first();

        return view('admin.blocksAndFloors.floors', compact('floors', 'blocks', 'block'));
    }

    public function store(Request $request)
    {
        $branchId = app(PropertyContext::class)->branchId();

        if (!$branchId) {
            return redirect()->back()->with('error', 'Please select or create a branch first.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'block_id' => [
                'required',
                Rule::exists('blocks', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
        ]);

        Floor::create([
            'name' => $request->name,
            'order' => $request->order ?? 0,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'block_id' => $request->block_id,
        ]);

        return redirect()->back()
            ->with('success', __('messages.floor_created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $floor = Floor::findOrFail($id);

        $floor->update([
            'name' => $request->name,
            'order' => $request->order,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()
            ->with('success', __('messages.floor_updated_successfully'));
    }

    public function delete(Floor $floor)
    {
        $floor->delete();

        return redirect()->back()->with('danger', __('messages.floor_deleted_successfully'));
    }
}
