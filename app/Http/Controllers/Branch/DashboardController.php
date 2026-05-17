<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\User;

class DashboardController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $branchQuery = $this->scopeBranchManagementForUser(Branch::query(), $user);
        $branchIds = (clone $branchQuery)->pluck('id');

        $branches = (clone $branchQuery)->get();
        $branchesCards = $this->scopeBranchManagementForUser(Branch::query(), $user)->paginate(10);
        $totalBranches = $branchIds->count();
        $activeBranches = Branch::whereIn('id', $branchIds)->where('status', 'Active')->count();
        $inActiveBranches = Branch::whereIn('id', $branchIds)->where('status', 'Inactive')->count();
        $totalManagers = User::where('role', 'manager')->whereIn('branch_id', $branchIds)->count();
        $brands = $this->isSuperAdmin($user)
            ? Brand::all()
            : Brand::where('company_id', $user->company_id)->get();
        $companies = $this->isSuperAdmin($user)
            ? Company::all()
            : ($user->company_id ? Company::whereKey($user->company_id)->get() : collect());

        return view('Admin.Backend.Branch.dashboard', compact(
            'branches',
            'totalBranches',
            'totalManagers',
            'activeBranches',
            'inActiveBranches',
            'branchesCards',
            'brands',
            'companies'
        ));
    }

    private function scopeBranchManagementForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}
