<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;

class DashboardController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $branchQuery = $this->scopeBranchesForUser(Branch::query(), $user);
        $branchIds = (clone $branchQuery)->pluck('id');

        $branches = (clone $branchQuery)->get();
        $branchesCards = $this->scopeBranchesForUser(Branch::query(), $user)->paginate(10);
        $totalBranches = $branchIds->count();
        $activeBranches = Branch::whereIn('id', $branchIds)->where('status', 'Active')->count();
        $inActiveBranches = Branch::whereIn('id', $branchIds)->where('status', 'Inactive')->count();
        $totalManagers = User::where('role', 'manager')->whereIn('branch_id', $branchIds)->count();

        return view('Admin.Backend.Branch.dashboard', compact('branches', 'totalBranches', 'totalManagers', 'activeBranches', 'inActiveBranches', 'branchesCards'));
    }
}
