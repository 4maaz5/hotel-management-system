<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\PenaltySetting;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index(Request $request)
    {
        $query = Penalty::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('value')) {
            $query->where('value', $request->value);
        }

        if ($request->filled('penalty_type')) {
            $query->where('penalty_type', $request->penalty_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $penalties = $query->latest()->get();

        return view('admin.penalty.index', compact('penalties'));
    }

    public function create()
    {
        return view('admin.penalty.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category' => 'required|string',
            'value' => 'nullable|numeric',
            'penalty_type' => 'nullable|string',
            'description' => 'required|string',
        ]);

        Penalty::create([
            'name' => $request->name,
            'category' => $request->category,
            'value' => $request->value,
            'penalty_type' => $request->penalty_type,
            'is_active' => $request->is_active ?? 1,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('setup-sidebar.penalty.index')
            ->with('success', __('messages.penalty_created_successfully'));
    }

    public function edit($id)
    {
        $penalty = Penalty::findOrfail($id);

        return view('admin.penalty.edit', compact('penalty'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category' => 'required|string',
            'value' => 'nullable|numeric',
            'penalty_type' => 'nullable|string',
            'description' => 'required|string',
        ]);

        $penalty = Penalty::findOrFail($id);

        $penalty->update([
            'name' => $request->name,
            'category' => $request->category,
            'value' => $request->value,
            'penalty_type' => $request->penalty_type,
            'is_active' => $request->is_active ?? 0,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('setup-sidebar.penalty.index')
            ->with('success', __('messages.penalty_updated_successfully'));
    }

    public function delete($id)
    {
        $penalty = Penalty::findOrfail($id);
        $penalty->delete();

        return redirect()->back()->with('danger', __('messages.penalty_deleted_successfully'));
    }

    public function updateSetting(Request $request)
    {
        $data = $request->only([
            'early_checkin_detection',
            'late_checkout_detection',
            'skip_cancel_no_show_penalty',
        ]);

        PenaltySetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return back()->with('success', __('messages.setting_updated_successfully'));
    }
}
