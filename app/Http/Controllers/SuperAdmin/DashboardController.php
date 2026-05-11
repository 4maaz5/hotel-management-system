<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $stats = [
            'totalTenants' => Tenant::count(),
            'activeTenants' => Tenant::query()
                ->where('status', 'active')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'expiredTenants' => Tenant::query()
                ->whereDate('end_date', '<', $today)
                ->count(),
            'suspendedTenants' => Tenant::query()
                ->where('status', 'suspended')
                ->count(),
            'tenantUsers' => User::query()
                ->whereNotNull('tenant_id')
                ->count(),
            'properties' => Property::query()->count(),
        ];

        $recentTenants = Tenant::query()
            ->latest()
            ->take(10)
            ->get();

        return view('super_admin.dashboard', compact('stats', 'recentTenants'));
    }
}
