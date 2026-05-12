<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AbsentController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     // Base query: Attendance with employee and branch
    //     $absentQuery = Attendance::with('employee.branch')
    //         ->where('status', 'Absent');

    //     // Role-based filtering
    //     if ($user->hasRole('manager')) {
    //         $absentQuery->whereHas('employee', function ($q) use ($user) {
    //             $q->where('branch_id', $user->branch_id);
    //         });
    //     } elseif ($user->hasRole('employee')) {
    //         $absentQuery->whereHas('employee', function ($q) use ($user) {
    //             $q->where('user_id', $user->id);
    //         });
    //     }

    //     //  PAGINATED list for table
    //     $absentEmployeesAll = (clone $absentQuery)->paginate(10);

    //     //  FULL list for cards (no pagination)
    //     $absentEmployees = (clone $absentQuery)->get();

    //     return view('Admin.Backend.EmployeeAbsent.index', compact(
    //         'absentEmployees',
    //         'absentEmployeesAll'
    //     ));
    // }
    public function index()
    {
        $user = Auth::user();

        // Base query: Attendance with employee and branch
        $absentQuery = Attendance::with('employee.branch')
            ->where('status', 'Absent');

        if (! $user->hasRole('super_admin')) {
            if ($user->branch_id) {
                $absentQuery->whereHas('employee', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                });
            } else {
                $absentQuery->whereHas('employee', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            }
        }

        // Paginated list for table
        $absentEmployeesAll = (clone $absentQuery)->paginate(10);

        // Full list for cards (paginated)
        $absentEmployees = (clone $absentQuery)->paginate(10);

        return view('Admin.Backend.EmployeeAbsent.index', compact(
            'absentEmployees',
            'absentEmployeesAll'
        ));
    }
}
