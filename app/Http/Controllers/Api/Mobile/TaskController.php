<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Housekeeper;
use App\Models\HousekeepingTask;
use App\Models\Property;
use App\Models\TaskMedia;
use App\Support\PropertyContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
        ]);

        $housekeeper = $this->currentHousekeeper($request);

        $tasks = HousekeepingTask::with(['unit', 'propertyFacility.facility', 'propertyFacility.category', 'taskType', 'media'])
            ->where('housekeeper_id', $housekeeper->id)
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($validated['priority'] ?? null, fn ($q, $priority) => $q->where('priority', $priority))
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderByDesc('start_date')
            ->paginate($request->integer('per_page', 20))
            ->through(fn (HousekeepingTask $task) => $this->taskPayload($task));

        return response()->json($tasks);
    }

    public function show(Request $request, $task)
    {
        $housekeeper = $this->currentHousekeeper($request);

        $task = HousekeepingTask::where('housekeeper_id', $housekeeper->id)
            ->where('id', $task)
            ->firstOrFail();

        return response()->json([
            'task' => $this->taskPayload($task->load(['unit', 'propertyFacility.facility', 'propertyFacility.category', 'taskType', 'media'])),
        ]);
    }

    public function updateStatus(Request $request, $task)
    {
        $housekeeper = $this->currentHousekeeper($request);

        $task = HousekeepingTask::where('housekeeper_id', $housekeeper->id)
            ->where('id', $task)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'media' => ['nullable'],
            'media.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi', 'max:20480'],
        ]);

        $property = $this->currentProperty();

        DB::transaction(function () use ($task, $validated, $property, $request) {
            $task->update([
                'status' => $validated['status'],
                'completed_date' => $validated['status'] === 'completed'
                    ? $this->nowForProperty($property)->toDateString()
                    : null,
            ]);

            if ($validated['status'] === 'completed' && $request->hasFile('media')) {
                $files = $request->file('media');
                if (! is_array($files)) {
                    $files = [$files];
                }

                $paths = [];
                try {
                    foreach ($files as $index => $file) {
                        $path = $file->store('task-media/' . $task->id, 'public');
                        $paths[] = $path;

                        $mime = $file->getMimeType();
                        $fileType = str_starts_with($mime, 'video/') ? 'video' : 'image';

                        TaskMedia::create([
                            'company_id' => $task->company_id,
                            'branch_id' => $task->branch_id,
                            'task_id' => $task->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $fileType,
                            'mime_type' => $mime,
                            'file_size' => $file->getSize(),
                            'sort_order' => $index,
                        ]);
                    }

                    foreach ($task->media()->whereNotIn('file_path', $paths)->get() as $old) {
                        Storage::disk('public')->delete($old->file_path);
                        $old->delete();
                    }
                } catch (\Throwable $e) {
                    foreach ($paths as $p) {
                        Storage::disk('public')->delete($p);
                    }
                    throw $e;
                }
            }
        });

        return response()->json([
            'message' => 'Task status updated successfully.',
            'task' => $this->taskPayload($task->fresh(['unit', 'propertyFacility.facility', 'propertyFacility.category', 'taskType', 'media'])),
        ]);
    }

    protected function currentHousekeeper(Request $request): Housekeeper
    {
        $housekeeper = Housekeeper::where('user_id', $request->user()->id)
            ->where('branch_id', app(PropertyContext::class)->branchId())
            ->where('is_active', true)
            ->first();

        if (! $housekeeper) {
            abort(403, 'This user is not an active housekeeper for the selected property.');
        }

        return $housekeeper;
    }

    protected function currentProperty(): Property
    {
        $property = Property::withoutGlobalScopes()->find(app(PropertyContext::class)->id());

        if (! $property) {
            abort(422, 'No property context is available.');
        }

        return $property;
    }

    protected function nowForProperty(Property $property): Carbon
    {
        $timezone = $property->time_zone ?: config('app.timezone');

        if (! in_array($timezone, timezone_identifiers_list(), true)) {
            $timezone = config('app.timezone', 'UTC');
        }

        return Carbon::now($timezone);
    }

    protected function taskPayload(HousekeepingTask $task): array
    {
        return [
            'id' => $task->id,
            'task_type' => $task->task_type,
            'task_type_name' => $task->taskType?->name,
            'priority' => $task->priority,
            'status' => $task->status,
            'description' => $task->description,
            'start_date' => optional($task->start_date)->toDateString(),
            'completed_date' => optional($task->completed_date)->toDateString(),
            'unit' => $task->unit ? [
                'id' => $task->unit->id,
                'unit_number' => $task->unit->unit_number,
                'housekeeping_status' => $task->unit->housekeeping_status,
            ] : null,
            'property_facility' => $task->propertyFacility ? [
                'id' => $task->propertyFacility->id,
                'facility' => $task->propertyFacility->facility?->name,
                'category' => $task->propertyFacility->category?->name,
            ] : null,
            'media' => $task->media->map(fn (TaskMedia $m) => [
                'id' => $m->id,
                'file_path' => $m->file_path,
                'url' => $m->url,
                'file_name' => $m->file_name,
                'file_type' => $m->file_type,
                'file_size' => $m->file_size,
                'sort_order' => $m->sort_order,
            ]),
        ];
    }
}
