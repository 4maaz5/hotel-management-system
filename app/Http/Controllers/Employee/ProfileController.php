<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Insurance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index($id)
    {
        $user = Auth::user();

        // Super Admin → can see any employee
        if ($user->hasRole('super_admin')) {
            $employee = Employee::findOrFail($id);
        }
        // All other roles → only same branch
        else {
            $employee = Employee::where('id', $id)
                ->where('branch_id', $user->branch_id)
                ->firstOrFail();
        }

        // Related data
        $attendances = Attendance::where('employee_id', $employee->id)->get();
        $insurances = Insurance::where('employee_id', $employee->id)->get();
        $documents = EmployeeDocument::where('employee_id', $employee->id)->get();

        return view(
            'Admin.Backend.EmployeeProfile.index',
            compact('employee', 'attendances', 'insurances', 'documents')
        );
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $employee = Employee::findOrFail($request->id);

        // Delete image if exists
        if ($employee->image && \Storage::disk('public')->exists($employee->image)) {
            \Storage::disk('public')->delete($employee->image);
        }

        // Delete associated user if exists
        if ($employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $user->delete();
            }
        }

        // Delete employee
        $employee->delete();

        return redirect()->route('dashboard.employee.index')->with('delete', __('messages.employee_profile_deleted_successfully'));
    }

    private function getEmployeeWithAccess($id)
    {
        $user = Auth::user();

        // Super Admin → can see any employee
        if ($user->hasRole('super_admin')) {
            return Employee::findOrFail($id);
        }

        // All other roles → only same branch
        return Employee::where('id', $id)
            ->where('branch_id', $user->branch_id)
            ->firstOrFail();
    }

    public function printProfile($id)
    {
        $employee = $this->getEmployeeWithAccess($id);

        $attendances = Attendance::where('employee_id', $employee->id)->get();
        $insurances = Insurance::where('employee_id', $employee->id)->get();
        $documents = EmployeeDocument::where('employee_id', $employee->id)->get();

        return view(
            'Admin.Backend.EmployeeProfile.pdf',
            compact('employee', 'attendances', 'insurances', 'documents')
        );
    }
}
