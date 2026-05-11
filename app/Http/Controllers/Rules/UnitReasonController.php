<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\UnitReason;
use Illuminate\Http\Request;

class UnitReasonController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitReason::query();

        if ($request->name) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $unitReasons = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.unit_reason.index', compact('unitReasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        UnitReason::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'comment_required' => $request->has('comment_required'),
        ]);

        return back()->with('success', __('messages.unit_reason_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unitReason = UnitReason::findOrFail($id);

        $unitReason->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'comment_required' => $request->has('comment_required'),
        ]);

        return back()->with('success', __('messages.unit_reason_updated_successfully'));
    }

    public function destroy($id)
    {
        $unitReason = UnitReason::findOrFail($id);
        $unitReason->delete();

        return back()->with('danger', __('messages.unit_reason_deleted_successfully'));
    }
}
