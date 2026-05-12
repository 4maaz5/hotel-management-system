<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class OverTimeController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     // Step 1: Get attendances filtered by role AND only where overtime > 0
    //     $attendancesQuery = Attendance::with('employee.branch')
    //         ->where('overtime_hours', '>', 0);

    //     if ($user->hasRole('manager')) {
    //         $attendancesQuery->whereHas('employee', function ($q) use ($user) {
    //             $q->where('branch_id', $user->branch_id);
    //         });
    //     } elseif ($user->hasRole('employee')) {
    //         $attendancesQuery->whereHas('employee', function ($q) use ($user) {
    //             $q->where('user_id', $user->id);
    //         });
    //     }
    //     // Super Admin sees all filtered attendances (overtime > 0)

    //     $attendances = $attendancesQuery->get();

    //     // Step 2: Calculate total overtime per employee
    //     $overtimeSummary = [];

    //     foreach ($attendances as $attendance) {
    //         $empId = $attendance->employee_id;
    //         $overtime = $attendance->overtime_hours ?? 0;

    //         if (! isset($overtimeSummary[$empId])) {
    //             $overtimeSummary[$empId] = [
    //                 'employee' => $attendance->employee,
    //                 'total_overtime' => 0,
    //             ];
    //         }
    //         $overtimeSummary[$empId]['total_overtime'] += $overtime;
    //     }

    //     // ------------------------------
    //     // ⭐ ADDED PAGINATION FOR CARDS
    //     // ------------------------------

    //     // Convert summary array to a collection
    //     $overtimeSummaryCollection = collect($overtimeSummary);

    //     // How many cards per page? (10 = 2 rows of 5 cards)
    //     $perPage = 15;

    //     // Current page number
    //     $page = request()->get('page', 1);

    //     // Paginated version of the same data
    //     $overtimeSummaryCards = new \Illuminate\Pagination\LengthAwarePaginator(
    //         $overtimeSummaryCollection->forPage($page, $perPage)->values(),
    //         $overtimeSummaryCollection->count(),
    //         $perPage,
    //         $page,
    //         ['path' => request()->url(), 'query' => request()->query()]
    //     );

    //     // Step 3: Pass both to view
    //     return view('Admin.Backend.OvertimeandAbsence.index', [
    //         'overtimeSummary' => $overtimeSummary,          // full data (table)
    //         'overtimeSummaryCards' => $overtimeSummaryCards, // paginated data (cards)
    //     ]);
    // }
    public function index()
    {
        $user = Auth::user();

        // Base query: only attendance with overtime > 0 and approved
        $attendancesQuery = Attendance::with('employee.branch')
            ->where('overtime_hours', '>', 0)
            ->where('overtime_status', 'Approved');

        if (! $user->hasRole('super_admin')) {
            if ($user->branch_id) {
                $attendancesQuery->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            } else {
                $attendancesQuery->whereHas('employee', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            }
        }

        // Get full attendance records
        $attendances = $attendancesQuery->get();

        // Calculate total overtime per employee
        $overtimeSummary = [];

        foreach ($attendances as $attendance) {
            $empId = $attendance->employee_id;
            $overtime = $attendance->overtime_hours ?? 0;

            if (! isset($overtimeSummary[$empId])) {
                $overtimeSummary[$empId] = [
                    'employee' => $attendance->employee,
                    'total_overtime' => 0,
                ];
            }
            $overtimeSummary[$empId]['total_overtime'] += $overtime;
        }

        $overtimeSummaryCollection = collect($overtimeSummary);
        $perPage = 15;
        $page = request()->get('page', 1);

        $overtimeSummaryCards = new \Illuminate\Pagination\LengthAwarePaginator(
            $overtimeSummaryCollection->forPage($page, $perPage)->values(),
            $overtimeSummaryCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('Admin.Backend.OvertimeandAbsence.index', [
            'overtimeSummary' => $overtimeSummary,
            'overtimeSummaryCards' => $overtimeSummaryCards,
        ]);
    }
}
