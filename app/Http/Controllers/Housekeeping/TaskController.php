<?php

namespace App\Http\Controllers\Housekeeping;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use App\Models\Unit;
use App\Models\PropertyFacility;
use App\Models\TaskType;
use App\Models\Housekeeper;
use App\Models\Floor;
use App\Models\UnitTypeCustomization;
use App\Models\Property;
use App\Models\PrintingOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // List all tasks with filters
    public function index(Request $request)
    {
        $query = HousekeepingTask::with(['unit', 'propertyFacility', 'taskType', 'housekeeper.user', 'media']);

        if ($request->task_type) {
            $query->where('task_type', $request->task_type);
        }
        if ($request->floor_id) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('floor_id', $request->floor_id);
            });
        }
        if ($request->unit_type_id) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_type_id', $request->unit_type_id);
            });
        }
        if ($request->unit_number) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_number', 'like', '%' . $request->unit_number . '%');
            });
        }
        if ($request->housekeeper_id) {
            $query->where('housekeeper_id', $request->housekeeper_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);

        $floors = Floor::all();
        $unitTypes = UnitTypeCustomization::all();
        $housekeepers = Housekeeper::with('user')->where('is_active', true)->get();
        $taskTypes = TaskType::where('is_active', true)->get();
        $propertyFacilities = PropertyFacility::all();

        $printingOption = \App\Models\PrintingOption::where('report_key', 'housekeeping_task')->first();

        return view('admin.housekeeping_task.index', compact(
            'tasks', 'floors', 'unitTypes', 'housekeepers', 'taskTypes', 'propertyFacilities', 'printingOption'
        ));
    }

    // Create new task
    public function store(Request $request)
    {
        $validated = $this->validateTaskData($request);

        HousekeepingTask::create([
            'task_type' => $validated['task_type'],
            'unit_id' => $validated['task_type'] === 'unit' ? $validated['unit_id'] : null,
            'property_facility_id' => $validated['task_type'] === 'property_facility' ? $validated['property_facility_id'] : null,
            'task_type_id' => $validated['task_type_id'],
            'housekeeper_id' => $validated['housekeeper_id'] ?? null,
            'created_by' => auth()->id(),
            'priority' => $validated['priority'],
            'status' => 'pending',
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? Carbon::today()->toDateString(),
        ]);

        return back()->with('success', __('messages.task_created_successfully'));
    }

    // Get task for edit
    public function edit(Request $request, $task)
    {
        $task = $this->resolveAccessibleTask($request, $task);
        $task->load('media');

        return response()->json([
            'id' => $task->id,
            'task_type' => $task->task_type,
            'unit_id' => $task->unit_id,
            'property_facility_id' => $task->property_facility_id,
            'task_type_id' => $task->task_type_id,
            'housekeeper_id' => $task->housekeeper_id,
            'priority' => $task->priority,
            'status' => $task->status,
            'description' => $task->description,
            'start_date' => optional($task->start_date)->format('Y-m-d'),
            'completed_date' => optional($task->completed_date)->format('Y-m-d'),
            'media' => $task->media->map(fn ($m) => [
                'id' => $m->id,
                'file_path' => $m->file_path,
                'url' => $m->url,
                'file_name' => $m->file_name,
                'file_type' => $m->file_type,
                'file_size' => $m->file_size,
                'sort_order' => $m->sort_order,
            ]),
        ]);
    }

    // Update task
    public function update(Request $request, $task)
    {
        $task = $this->resolveAccessibleTask($request, $task);
        $validated = $this->validateTaskData($request, $task, true);

        $task->update([
            'task_type' => $validated['task_type'],
            'unit_id' => $validated['task_type'] === 'unit' ? $validated['unit_id'] : null,
            'property_facility_id' => $validated['task_type'] === 'property_facility' ? $validated['property_facility_id'] : null,
            'task_type_id' => $validated['task_type_id'],
            'housekeeper_id' => $validated['housekeeper_id'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'completed_date' => $validated['status'] === 'completed' ? Carbon::today()->toDateString() : null,
        ]);

        return back()->with('success', __('messages.task_updated_successfully'));
    }

    // Delete task
    public function destroy(Request $request, $task)
    {
        $task = $this->resolveAccessibleTask($request, $task);
        $task->delete();
        return back()->with('danger', __('messages.task_deleted_successfully'));
    }

    // Print tasks
    public function print(Request $request)
    {
        $query = HousekeepingTask::with(['unit', 'propertyFacility', 'taskType', 'housekeeper.user', 'creator', 'media']);

        // Apply same filters as index
        if ($request->task_type) {
            $query->where('task_type', $request->task_type);
        }
        if ($request->floor_id) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('floor_id', $request->floor_id);
            });
        }
        if ($request->unit_type_id) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_type_id', $request->unit_type_id);
            });
        }
        if ($request->unit_number) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_number', 'like', '%' . $request->unit_number . '%');
            });
        }
        if ($request->housekeeper_id) {
            $query->where('housekeeper_id', $request->housekeeper_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->task_type_id) {
            $query->where('task_type_id', $request->task_type_id);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        // Get printing options
        $printingOption = \App\Models\PrintingOption::where('report_key', 'housekeeping_task')->first();
        $globalSetting = \App\Models\PrintingOption::first();

        $property = \App\Models\Property::current();

        $filtersApplied = $request->hasAny(['task_type', 'floor_id', 'unit_type_id', 'unit_number', 'status', 'priority', 'housekeeper_id']);

        return view('admin.housekeeping_task.print', compact('tasks', 'printingOption', 'globalSetting', 'property', 'filtersApplied'));
    }

    // Get units for dropdown
    public function getUnits(Request $request)
    {
        $units = Unit::where('is_active', true)
            ->when($request->floor_id, fn($q) => $q->where('floor_id', $request->floor_id))
            ->when($request->unit_type_id, fn($q) => $q->where('unit_type_id', $request->unit_type_id))
            ->when($request->search, fn($q) => $q->where('unit_number', 'like', '%' . $request->search . '%'))
            ->limit(20)
            ->get();

        return response()->json($units);
    }

    protected function validateTaskData(Request $request, ?HousekeepingTask $task = null, bool $includeStatus = false): array
    {
        $payload = [
            'task_type' => $request->input('task_type', $task?->task_type),
            'unit_id' => $request->input('unit_id', $task?->unit_id),
            'property_facility_id' => $request->input('property_facility_id', $task?->property_facility_id),
            'task_type_id' => $request->input('task_type_id', $task?->task_type_id),
            'housekeeper_id' => $request->input('housekeeper_id', $task?->housekeeper_id),
            'priority' => $request->input('priority', $task?->priority),
            'status' => $request->input('status', $task?->status),
            'description' => $request->input('description', $task?->description),
            'start_date' => $request->input('start_date', optional($task?->start_date)->format('Y-m-d')),
        ];

        $rules = [
            'task_type' => ['required', Rule::in(['unit', 'property_facility'])],
            'unit_id' => ['nullable', 'required_if:task_type,unit', 'exists:units,id'],
            'property_facility_id' => ['nullable', 'required_if:task_type,property_facility', 'exists:property_facilities,id'],
            'task_type_id' => ['required', 'exists:housekeeping_task_types,id'],
            'housekeeper_id' => ['nullable', 'exists:housekeepers,id'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
        ];

        if ($includeStatus) {
            $rules['status'] = ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])];
        }

        return validator($payload, $rules)->validate();
    }

    protected function resolveAccessibleTask(Request $request, $taskId): HousekeepingTask
    {
        $task = HousekeepingTask::withoutGlobalScopes()->findOrFail($taskId);
        $user = $request->user();

        if ($user && ! (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            if ($task->tenant_id && $user->tenant_id && (int) $task->tenant_id !== (int) $user->tenant_id) {
                abort(404);
            }

            if ($task->property_id && ! $user->canAccessProperty((int) $task->property_id)) {
                abort(404);
            }
        }

        return $task;
    }
}
