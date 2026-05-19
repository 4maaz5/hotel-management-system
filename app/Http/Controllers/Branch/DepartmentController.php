<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $departments = Department::with('branch')->get();
            $departmentCards = Department::with('branch')->paginate(10);
        } else {
            $companyBranchIds = Branch::where('company_id', $user->company_id)->pluck('id');
            $departments = Department::with('branch')->whereIn('branch_id', $companyBranchIds)->get();
            $departmentCards = Department::with('branch')->whereIn('branch_id', $companyBranchIds)->paginate(10);
        }

        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();

        return view('Admin.Backend.Department.index', compact('departments', 'branches', 'departmentCards'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user)),
            ],
            'dep_name' => 'required|string|max:255',
        ]);

        $branch = Branch::find($request->branch_id);
        if (! $branch || ! $this->userCanAccessBranch((int) $branch->id, $user)) {
            abort(403);
        }

        $department = Department::create([
            'company_id' => $branch->company_id,
            'branch_id' => $request->branch_id,
            'name' => $request->dep_name,
        ]);

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
        $user = auth()->user();
        $request->validate([
            'id' => 'required|exists:departments,id',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user)),
            ],
            'dep_name' => 'required|string|max:255',
        ]);

        $branch = Branch::find($request->branch_id);
        if (! $branch || ! $this->userCanAccessBranch((int) $branch->id, $user)) {
            abort(403);
        }

        $department = Department::findOrFail($request->id);
        if (! $this->userCanAccessBranch((int) $department->branch_id, $user)) {
            abort(403);
        }

        $department->update([
            'company_id' => $branch->company_id,
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
                'branch' => $department->branch->name,
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $department = Department::findOrFail($request->department_id);

        if (! $this->userCanAccessBranch((int) $department->branch_id, $user)) {
            abort(403);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.department_deleted_successfully'),
        ]);
    }

    public function filter(Request $request)
    {
        $user = auth()->user();

        $query = Department::with('branch');

        if (! $user->isSuperAdmin()) {
            $branchIds = $this->accessibleBranchIds($user);
            $query->whereIn('branch_id', $branchIds);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('branch_id')) {
            if (! $this->userCanAccessBranch((int) $request->branch_id, $user)) {
                return response()->json(['html' => ''], 403);
            }

            $query->where('branch_id', $request->branch_id);
        }

        $departments = $query->orderBy('name', 'asc')->get();

        $html = view('Admin.Backend.partials.departments_rows', compact('departments'))->render();

        return response()->json(['html' => $html]);
    }
}
