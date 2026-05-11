<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $departmentCards = Department::paginate(10);
        $branches = Branch::all();

        return view('Admin.Backend.Department.index', compact('departments', 'branches', 'departmentCards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'dep_name' => 'required|string|max:255',
        ]);

        $department = Department::create([
            'branch_id' => $request->branch_id,
            'name' => $request->dep_name,
        ]);

        // Include branch name for table display
        $branchName = $department->branch->name;

        return response()->json([
            'success' => true,
            'message' => __('messages.department_created_successfully'),
            'data' => [
                'id' => $department->id,
                'name' => $department->name,
                'branch' => $branchName,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:departments,id',
            'branch_id' => 'required|exists:branches,id',
            'dep_name' => 'required|string|max:255',
        ]);

        $department = Department::findOrFail($request->id);
        $department->update([
            'name' => $request->dep_name,
            'branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.department_updated_successfully'),
            'data' => [
                'id' => $department->id,
                'name' => $department->name,
                'branch_id' => $department->branch_id,
                'branch' => $department->branch->name, // important
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $department = Department::find($request->department_id);
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.department_deleted_successfully'),
        ]);
    }

    public function filter(Request $request)
    {
        $query = Department::with('branch');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $departments = $query->orderBy('name', 'asc')->get();

        $html = view('Admin.Backend.partials.departments_rows', compact('departments'))->render();

        return response()->json(['html' => $html]);
    }
}
