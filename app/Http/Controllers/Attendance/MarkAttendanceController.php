<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MarkAttendanceController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     if ($user->hasRole('super_admin')) {
    //         // Super Admin sees all attendance
    //         $attendances = Attendance::with('employee')->latest()->get();
    //         $attendancesCards = Attendance::with('employee')->latest()->paginate(10); // paginated cards
    //         $employees = Employee::all();
    //         $branches = Branch::all(); // send all branches for filter

    //     } elseif ($user->hasRole('manager')) {
    //         // Manager sees attendance and employees for their branch
    //         $attendances = Attendance::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })->with('employee')->latest()->get();

    //         $attendancesCards = Attendance::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })->with('employee')->latest()->paginate(10); // paginated cards

    //         $employees = Employee::where('branch_id', $user->branch_id)->get();
    //         $branches = Branch::where('id', $user->branch_id)->get(); // manager branch

    //     } elseif ($user->hasRole('employee')) {
    //         // Employee sees only their own attendance
    //         $attendances = Attendance::whereHas('employee', function ($query) use ($user) {
    //             $query->where('user_id', $user->id);
    //         })->with('employee')->latest()->get();

    //         $attendancesCards = Attendance::whereHas('employee', function ($query) use ($user) {
    //             $query->where('user_id', $user->id);
    //         })->with('employee')->latest()->paginate(10); // paginated cards

    //         $employees = Employee::where('user_id', $user->id)->get();
    //         $branches = Branch::where('id', $user->branch_id)->get(); // employee branch

    //     } else {
    //         $attendances = collect();
    //         $attendancesCards = collect();
    //         $employees = collect();
    //         $branches = collect();
    //     }

    //     return view('Admin.Backend.EmployeeAttendance.index', compact(
    //         'attendances',
    //         'employees',
    //         'branches',
    //         'attendancesCards'
    //     ));
    // }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $attendances = Attendance::with('employee')->latest()->get();
            $attendancesCards = Attendance::with('employee')->latest()->paginate(10);
            $employees = Employee::all();
            $branches = Branch::all();
        } elseif ($user->branch_id) {
            $attendances = Attendance::whereHas('employee', function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })->with('employee')->latest()->get();

            $attendancesCards = Attendance::whereHas('employee', function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })->with('employee')->latest()->paginate(10);

            $employees = Employee::where('branch_id', $user->branch_id)->get();
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $attendances = Attendance::whereHas('employee', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->with('employee')->latest()->get();

            $attendancesCards = Attendance::whereHas('employee', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            })->with('employee')->latest()->paginate(10);

            $employees = Employee::where('company_id', $user->company_id)->get();
            $branches = Branch::all();
        }

        return view('Admin.Backend.EmployeeAttendance.index', compact(
            'attendances',
            'employees',
            'branches',
            'attendancesCards'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'status' => 'required|in:Present,Absent,Leave',
            'overtime_hours' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Prevent duplicate attendance
        $exists = Attendance::where('employee_id', $data['employee_id'])
            ->where('date', $data['date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['date' => [__('messages.attendance_for_this')]],
            ], 409);
        }

        Attendance::create([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
            'check_in' => $data['clock_in'] ?? null,
            'check_out' => $data['clock_out'] ?? null,
            'status' => $data['status'],
            'overtime_status' => 'approved',
            'overtime_hours' => $data['overtime_hours'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.attendance_added'),
        ]);
    }

    public function update(Request $request)
    {
        $attendance = Attendance::find($request->id);

        if (! $attendance) {
            return response()->json(['status' => 'error', 'message' => __('messages.attendance_not_found')], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable|after:clock_in',
            'status' => 'required|in:Present,Absent,Leave',
            'overtime_status' => 'required|in:pending,approved,cancelled',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $attendance->update([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
            'check_in' => $data['clock_in'] ?? null,
            'check_out' => $data['clock_out'] ?? null,
            'status' => $data['status'],
            'overtime_status' => $data['overtime_status'],
            'overtime_hours' => $data['overtime_hours'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.attendance_updated'),
        ]);
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attendance_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete attendance record.',
            ], 500);
        }
    }

    // public function filter(Request $request)
    // {
    //     $query = Attendance::query();

    //     // Filter by name
    //     if ($request->filled('name')) {
    //         $query->whereHas('employee', function ($q) use ($request) {
    //             $q->where('first_name', 'like', '%'.$request->name.'%')
    //                 ->orWhere('last_name', 'like', '%'.$request->name.'%');
    //         });
    //     }

    //     // Existing filters
    //     if ($request->filled('employee_id')) {
    //         $query->where('employee_id', $request->employee_id);
    //     }
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('date', '>=', $request->start_date);
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('date', '<=', $request->end_date);
    //     }

    //     $attendances = $query->orderBy('date', 'desc')->get();

    //     // return the partial view
    //     return view('Admin.Backend.partials.attendance', compact('attendances'));

    // }

    public function filter(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with('employee');

        // Branch-based access
        if ($user->hasRole('super_admin')) {
            if ($request->filled('branch_id')) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }
        } elseif ($user->branch_id) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } else {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        }

        // Filter by employee name
        if ($request->filled('name')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->name.'%')
                    ->orWhere('last_name', 'like', '%'.$request->name.'%');
            });
        }

        // Other existing filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // Return partial view
        return view('Admin.Backend.partials.attendance', compact('attendances'));
    }

    public function scanView()
    {
        return view('Admin.Backend.EmployeeAttendance.scan');
    }

    public function scan(Request $request)
    {
        \Log::info('Scan request received', ['qr_code' => $request->qr_code]);

        try {
            $request->validate([
                'qr_code' => 'required|string',
            ]);

            $employee = Employee::with('shift')->where('qr_code', $request->qr_code)->first();

            if (! $employee) {
                return response()->json([
                    'message' => 'Invalid QR Code',
                    'status' => 'error',
                ], 404);
            }

            $date = now()->toDateString();
            $currentTime = now();

            if (! $employee->shift) {
                return response()->json([
                    'message' => __('messages.attendance_not_found'),
                    'status' => 'error',
                ], 403);
            }

            $shiftStart = \Carbon\Carbon::parse($date.' '.$employee->shift->start_time);
            $shiftEnd = \Carbon\Carbon::parse($date.' '.$employee->shift->end_time);

            // Optional: shift window enforcement (can be adjusted)
            $earlyLimit = $shiftStart->copy()->subMinutes(0);
            $lateLimit = $shiftEnd->copy()->addMinutes(0);

            if ($currentTime->lt($earlyLimit) || $currentTime->gt($lateLimit->copy()->addHours(4))) {
                // optionally allow 4h extra for late checkout
                return response()->json([
                    'message' => __('messages.attendance_cannot_mark_outside_shift'),
                    'status' => 'error',
                    'type' => 'outside_shift',
                ], 403);
            }

            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            // Cooldown check
            $cooldownMinutes = 10;
            if ($attendance && $attendance->last_scan_at) {
                $lastScan = \Carbon\Carbon::parse($attendance->last_scan_at);
                if ($currentTime->diffInMinutes($lastScan) < $cooldownMinutes) {
                    return response()->json([
                        'message' => __('please_wait'),
                        'status' => 'error',
                        'type' => 'cooldown',
                    ]);
                }
            }

            if (! $attendance) {
                // First scan → check-in
                $status = 'Present';

                // 1-hour late logic
                $shiftWithGrace = $shiftStart->copy()->addHour();
                if ($currentTime->gt($shiftWithGrace)) {
                    $status = 'Absent';
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in' => $currentTime->format('H:i'),
                    'status' => $status,
                    'overtime_status' => 'pending',
                    'last_scan_at' => $currentTime,
                ]);

                return response()->json([
                    'message' => $employee->first_name.__('messages.checked_in_successfully'),
                    'status' => 'success',
                    'type' => 'check_in',
                    'employee_name' => $employee->first_name,
                    'attendance_status' => $status,
                ]);

            } elseif (! $attendance->check_out) {
                // Second scan → check-out

                // Initialize overtime to 0
                $overtimeHours = 0;

                if ($attendance->status !== 'Absent' && $currentTime->gt($shiftEnd)) {
                    $overtimeHours = round($shiftEnd->diffInSeconds($currentTime) / 3600, 2);
                }

                // Update attendance with check-out + overtime
                $attendance->update([
                    'check_out' => $currentTime->format('H:i'),
                    'last_scan_at' => $currentTime,
                    'overtime_hours' => $overtimeHours,
                    'overtime_status' => 'pending',
                ]);

                return response()->json([
                    'message' => $employee->first_name.__('messages.checked_out_successfully'),
                    'status' => 'success',
                    'type' => 'check_out',
                    'employee_name' => $employee->first_name,
                    'overtime_hours' => $overtimeHours,
                ]);
            } else {
                // Already checked out, just update last scan
                $attendance->update([
                    'last_scan_at' => $currentTime,
                ]);

                return response()->json([
                    'message' => __('messages.attendance_already_completed'),
                    'status' => 'already_marked',
                    'type' => 'present',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Attendance scan error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Server error: '.$e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }

    // public function fetch()
    // {
    //     $user = Auth::user();

    //     $attendances = Attendance::with('employee');

    //     if ($user->hasRole('super_admin')) {
    //         $attendances = $attendances->latest()->take(10)->get();
    //     } elseif ($user->hasRole('manager')) {
    //         if (! $user->branch_id) {
    //             return response()->json([], 200); // avoid null branch_id
    //         }
    //         $attendances = $attendances
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()->take(10)->get();
    //     } else {
    //         $attendances = collect();
    //     }

    //     return response()->json($attendances);
    // }

    public function fetch()
    {
        $user = Auth::user();

        $attendances = Attendance::with('employee');

        if ($user->hasRole('super_admin')) {
            $attendances = $attendances->latest()->take(10)->get();
        } elseif ($user->branch_id) {
            $attendances = $attendances
                ->whereHas('employee', function ($query) use ($user) {
                    $query->where('branch_id', $user->branch_id);
                })
                ->latest()
                ->get();
        } else {
            $attendances = $attendances
                ->whereHas('employee', function ($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                })
                ->latest()
                ->get();
        }

        return response()->json($attendances);
    }
}
