<?php

namespace App\Http\Controllers\HouseKeepingSetting;

use App\Http\Controllers\Controller;
use App\Models\Housekeeper;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffAttendance::with(['user', 'housekeeper.user', 'property', 'editor']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('attendance_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('attendance_date', '<=', $request->to);
        }

        $attendances = $query->orderByDesc('attendance_date')
            ->orderByDesc('check_in_at')
            ->paginate(15)
            ->withQueryString();

        $housekeepers = Housekeeper::with('user')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get();

        return view('admin.staff_attendance.index', compact('attendances', 'housekeepers'));
    }

    public function update(Request $request, StaffAttendance $attendance)
    {
        $validated = $request->validate([
            'check_in_at' => ['required', 'date'],
            'check_out_at' => ['nullable', 'date', 'after_or_equal:check_in_at'],
            'status' => ['required', Rule::in(['checked_in', 'checked_out', 'adjusted'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $checkInAt = Carbon::parse($validated['check_in_at']);

        $attendance->update([
            'attendance_date' => $checkInAt?->toDateString() ?? $attendance->attendance_date,
            'check_in_at' => $checkInAt,
            'check_out_at' => ! empty($validated['check_out_at']) ? Carbon::parse($validated['check_out_at']) : null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'edited_by' => auth()->id(),
        ]);

        return back()->with('success', __('messages.setting_updated_successfully'));
    }

    public function destroy(StaffAttendance $attendance)
    {
        $attendance->forceFill(['deleted_by' => auth()->id()])->save();
        $attendance->delete();

        return back()->with('danger', __('messages.setting_updated_successfully'));
    }
}
