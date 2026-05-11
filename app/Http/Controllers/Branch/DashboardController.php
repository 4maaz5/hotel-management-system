<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $branchesCards = Branch::paginate(10);
        $totalBranches = Branch::count();
        $activeBranches = Branch::where('status', 'Active')->count();
        $inActiveBranches = Branch::where('status', 'Inactive')->count();
        $totalManagers = User::where('role', 'manager')->count();

        return view('Admin.Backend.Branch.dashboard', compact('branches', 'totalBranches', 'totalManagers', 'activeBranches', 'inActiveBranches', 'branchesCards'));
    }
}
