<?php

namespace App\Http\Controllers\HouseKeepingSetting;

use App\Http\Controllers\Controller;
use App\Models\TaskType;
use Illuminate\Http\Request;

class TaskTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskType::query();

        if ($request->name) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $taskTypes = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.task_type.index', compact('taskTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        TaskType::create([
            'name' => $request->name,
            'is_active' => true,
            'is_routine' => $request->has('is_routine'),
        ]);

        return back()->with('success', __('messages.task_type_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $taskType = TaskType::findOrFail($id);

        $taskType->update([
            'is_active' => $request->has('is_active'),
            'is_routine' => $request->has('is_routine'),
        ]);

        return back()->with('success', __('messages.task_type_updated_successfully'));
    }

    public function destroy($id)
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->delete();

        return back()->with('danger', __('messages.task_type_deleted_successfully'));
    }

    public function toggleStatus($id)
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->update(['is_active' => ! $taskType->is_active]);

        return back()->with('success', __('messages.task_type_status_updated_successfully'));
    }
}
