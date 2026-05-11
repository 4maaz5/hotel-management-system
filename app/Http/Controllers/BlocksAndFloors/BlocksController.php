<?php

namespace App\Http\Controllers\BlocksAndFloors;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Support\PropertyContext;
use Illuminate\Http\Request;

class BlocksController extends Controller
{
    public function index(Request $request)
    {
        $query = Block::query();
        $hasBlocks = Block::query()->exists();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        $blocks = $query->orderBy('id', 'desc')->paginate(10);

        $blocks->appends($request->query());

        return view('admin.blocksAndFloors.blocks', compact('blocks', 'hasBlocks'));
    }

    public function store(Request $request)
    {
        if (!app(PropertyContext::class)->id()) {
            return redirect()->back()->with('error', __('messages.plz_select_or_create_a_branch_first'));
        }

        if (Block::query()->exists()) {
            return redirect()
                ->back()
                ->withErrors([
                    'block' => 'Only one block can be created for the selected branch.',
                ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Block::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.block_created_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $block = Block::findOrFail($id);

        $block->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.block_updated_successfully'));
    }

    public function delete($id)
    {
        $block = Block::findOrFail($id);

        $block->delete();

        return redirect()->back()->with('danger', __('messages.block_deleted_successfully'));
    }
}
