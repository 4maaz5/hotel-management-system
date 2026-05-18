<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $employeesQuery = $this->scopeEmployeesForDashboard(Employee::query(), $user);
        $attendanceQuery = $this->scopeAttendancesForDashboard(Attendance::with('employee'), $user);
        $attendanceCountQuery = $this->scopeAttendancesForDashboard(Attendance::query(), $user);

        $employees = (clone $employeesQuery)->orderBy('first_name')->get();
        $attendances = (clone $attendanceQuery)->latest()->get();
        $attendancesCards = (clone $attendanceQuery)->latest()->paginate(10);

        $totalEmployees = (clone $employeesQuery)->count();
        $totalAttendance = (clone $attendanceCountQuery)->count();
        $presentToday = (clone $attendanceCountQuery)->where('status', 'Present')->count();
        $absentToday = (clone $attendanceCountQuery)->where('status', 'Absent')->count();

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

    private function scopeAttendancesForDashboard($query, $user)
    {
        return $query->whereHas('employee', function ($employeeQuery) use ($user) {
            $this->scopeEmployeesForDashboard($employeeQuery, $user);
        });
    }

    private function scopeEmployeesForDashboard($query, $user)
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        $tenantId = app(TenantContext::class)->id() ?: $user->company_id;

        if ($tenantId) {
            return $query->where('company_id', $tenantId);
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }
}
