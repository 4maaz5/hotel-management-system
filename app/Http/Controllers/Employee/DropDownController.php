<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Department;
use App\Models\Shift;

class DropDownController extends Controller
{
    use ScopesTenantAccess;

    public function getBrands($companyId)
    {
        abort_unless($this->isSuperAdmin(auth()->user()) || (int) $companyId === (int) auth()->user()->company_id, 403);

        $brands = Brand::where('company_id', $companyId)->get();

        return response()->json($brands);
    }

    public function getBranches($brandId)
    {
        $user = auth()->user();
        $branches = $this->scopeBranchesForUser(Branch::where('brand_id', $brandId), $user)->get();

        return response()->json($branches);
    }

    public function getDepartments($branchId)
    {
        abort_unless($this->userCanAccessBranch((int) $branchId, auth()->user()), 403);

        $employees = Department::where('branch_id', $branchId)->get();

        return response()->json($employees);
    }

    public function getShifts($branchId)
    {
        abort_unless($this->userCanAccessBranch((int) $branchId, auth()->user()), 403);

        $shifts = Shift::where('branch_id', $branchId)
            ->get(['id', 'name', 'start_time', 'end_time']);

        return response()->json($shifts);
    }
}
