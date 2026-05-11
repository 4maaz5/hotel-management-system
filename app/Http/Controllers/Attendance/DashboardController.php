<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $today = now()->toDateString();
    //     $user = Auth::user();

    //     $employees = Employee::all();

    //     // Default values
    //     $totalEmployees = 0;
    //     $presentToday = 0;
    //     $totalAttendance = 0;
    //     $attendancesCards = collect(); // default empty collection

    //     // ROLE: SUPER ADMIN
    //     if ($user->hasRole('super_admin')) {
    //         $attendances = Attendance::with('employee')->latest()->get();
    //         $attendancesCards = Attendance::with('employee')->latest()->paginate(10);

    //         $totalEmployees = Employee::count();

    //         $presentToday = Attendance::whereDate('date', $today)
    //             ->whereNotNull('check_in')
    //             ->count();

    //         $totalAttendance = Attendance::whereDate('date', $today)->count();
    //     }

    //     // ROLE: MANAGER
    //     elseif ($user->hasRole('manager')) {
    //         $branchId = $user->branch_id;

    //         $attendances = Attendance::whereHas('employee', function ($query) use ($branchId) {
    //             $query->where('branch_id', $branchId);
    //         })->with('employee')->latest()->get();

    //         $attendancesCards = Attendance::whereHas('employee', function ($query) use ($branchId) {
    //             $query->where('branch_id', $branchId);
    //         })->with('employee')->latest()->paginate(10);

    //         $totalEmployees = Employee::where('branch_id', $branchId)->count();

    //         $presentToday = Attendance::whereDate('date', $today)
    //             ->whereNotNull('check_in')
    //             ->whereHas('employee', function ($query) use ($branchId) {
    //                 $query->where('branch_id', $branchId);
    //             })
    //             ->count();

    //         $totalAttendance = Attendance::whereDate('date', $today)
    //             ->whereHas('employee', function ($query) use ($branchId) {
    //                 $query->where('branch_id', $branchId);
    //             })
    //             ->count();
    //     }

    //     // ROLE: EMPLOYEE
    //     else {
    //         $employee = Employee::where('user_id', $user->id)->first();

    //         $attendances = Attendance::where('employee_id', $employee->id)
    //             ->with('employee')
    //             ->latest()
    //             ->get();

    //         $attendancesCards = Attendance::where('employee_id', $employee->id)
    //             ->with('employee')
    //             ->latest()
    //             ->paginate(10);

    //         $totalEmployees = 1;

    //         $presentToday = Attendance::whereDate('date', $today)
    //             ->where('employee_id', $employee->id)
    //             ->whereNotNull('check_in')
    //             ->count();

    //         $totalAttendance = Attendance::whereDate('date', $today)
    //             ->where('employee_id', $employee->id)
    //             ->count();
    //     }

    //     // Common Calculations
    //     $absentToday = max($totalEmployees - $presentToday, 0);

    //     $attendancePercentage = $totalEmployees > 0
    //         ? round(($presentToday / $totalEmployees) * 100, 2)
    //         : 0;

    //     // Return View
    //     return view('Admin.Backend.EmployeeAttendance.dashboard', compact(
    //         'attendances',
    //         'employees',
    //         'totalAttendance',
    //         'presentToday',
    //         'absentToday',
    //         'attendancePercentage',
    //         'attendancesCards'
    //     ));
    // }

    public function index()
    {
        $today = now()->toDateString();
        $user = Auth::user();

        // Default values
        $totalEmployees = 0;
        $presentToday = 0;
        $totalAttendance = 0;
        $attendancesCards = collect(); // default empty collection

        if ($user->hasRole('super_admin')) {
            // Super admin sees all
            $attendances = Attendance::with('employee')->latest()->get();
            $attendancesCards = Attendance::with('employee')->latest()->paginate(10);

            $totalEmployees = Employee::count();

            $presentToday = Attendance::whereDate('date', $today)
                ->whereNotNull('check_in')
                ->count();

            $totalAttendance = Attendance::whereDate('date', $today)->count();

            $employees = Employee::all();
        } else {
            // Non-super admin → only their branch
            $branchId = $user->branch_id;

            $attendances = Attendance::whereHas('employee', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
                ->with('employee')
                ->latest()
                ->get();

            $attendancesCards = Attendance::whereHas('employee', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
                ->with('employee')
                ->latest()
                ->paginate(10);

            $totalEmployees = Employee::where('branch_id', $branchId)->count();

            $presentToday = Attendance::whereDate('date', $today)
                ->whereNotNull('check_in')
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();

            $totalAttendance = Attendance::whereDate('date', $today)
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();

            $employees = Employee::where('branch_id', $branchId)->get();
        }

        // Common Calculations
        $absentToday = max($totalEmployees - $presentToday, 0);

        $attendancePercentage = $totalEmployees > 0
            ? round(($presentToday / $totalEmployees) * 100, 2)
            : 0;

        return view('Admin.Backend.EmployeeAttendance.dashboard', compact(
            'attendances',
            'employees',
            'totalAttendance',
            'presentToday',
            'absentToday',
            'attendancePercentage',
            'attendancesCards'
        ));
    }
}
