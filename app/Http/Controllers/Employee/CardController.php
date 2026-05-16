<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    use ScopesTenantAccess;

    // public function index()
    // {
    //     $user = Auth::user();
    //     if ($user->hasRole('super_admin')) {
    //         $employees = Employee::all();
    //         $employeeCards = Employee::paginate(10);
    //         $branches = Branch::all();
    //     } elseif ($user->hasRole('manager')) {
    //         $employees = Employee::where('branch_id', $user->branch_id)->get();
    //         $employeeCards = Employee::where('branch_id', $user->branch_id)->paginate(10);
    //         $branches = Branch::where('id', $user->branch_id)->get();
    //     } elseif ($user->hasRole('employee')) {
    //         $employees = Employee::where('email', $user->email)->get();
    //         $branches = null;
    //     }

    //     return view('Admin.Backend.EmployeeCard.index', compact('employees', 'employeeCards', 'branches'));
    // }

    public function index()
    {
        $user = Auth::user();

        // Initialize variables
        $employees = collect();
        $employeeCards = collect();
        $branches = collect();

        if ($user->hasRole('super_admin')) {
            $employees = Employee::all(); // all employees for modals
            $employeeCards = Employee::paginate(10);
            $tableEmployees = Employee::paginate(10); // paginated for table
            $branches = Branch::all();
        } else {
            $branchId = $user->branch_id;

            if ($branchId) {
                $employees = Employee::where('branch_id', $branchId)->get();
                $employeeCards = Employee::where('branch_id', $branchId)->paginate(10);
                $tableEmployees = Employee::where('branch_id', $branchId)->paginate(10);
                $branches = Branch::where('id', $branchId)->get();
            } else {
                $employees = Employee::where('company_id', $user->company_id)->get();
                $employeeCards = Employee::where('company_id', $user->company_id)->paginate(10);
                $tableEmployees = Employee::where('company_id', $user->company_id)->paginate(10);
                $branches = Branch::where('company_id', $user->company_id)->get();
            }
        }

        return view('Admin.Backend.EmployeeCard.index', compact(
            'employees',
            'employeeCards',
            'tableEmployees',
            'branches'
        ));
    }
}
