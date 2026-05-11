<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Department;

class DropDownController extends Controller
{
    public function getBrands($companyId)
    {
        $brands = Brand::where('company_id', $companyId)->get();

        return response()->json($brands);
    }

    public function getBranches($brandId)
    {
        $branches = Branch::where('brand_id', $brandId)->get();

        return response()->json($branches);
    }

    public function getDepartments($branchId)
    {
        $employees = Department::where('branch_id', $branchId)->get();

        return response()->json($employees);
    }
}
