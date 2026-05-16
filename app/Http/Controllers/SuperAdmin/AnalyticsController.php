<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AnalyticsController extends Controller
{
    public function index()
    {
        $today = today();

        $activeTenants = Tenant::query()
            ->with('plan')
            ->where('subscription_status', 'active')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $monthlyRecurringRevenue = $activeTenants->sum(function (Tenant $tenant): float {
            $plan = $tenant->plan;

            if (! $plan) {
                return 0;
            }

            $price = (float) $plan->price;

            return $plan->billing_period === 'yearly' ? $price / 12 : $price;
        });

        $stats = [
            'monthlyRecurringRevenue' => $monthlyRecurringRevenue,
            'annualRecurringRevenue' => $monthlyRecurringRevenue * 12,
            'activeTenants' => $activeTenants->count(),
            'totalTenants' => Tenant::count(),
            'totalUsers' => Schema::hasColumn('users', 'company_id')
                ? User::query()->whereNotNull('company_id')->count()
                : 0,
            'totalProperties' => Schema::hasTable('properties') ? Property::count() : 0,
            'averageRevenuePerTenant' => $activeTenants->count() > 0
                ? $monthlyRecurringRevenue / $activeTenants->count()
                : 0,
        ];

        $tenantStatusCounts = Tenant::query()
            ->selectRaw('subscription_status, COUNT(*) as total')
            ->groupBy('subscription_status')
            ->pluck('total', 'subscription_status')
            ->all();

        $planDistribution = SubscriptionPlan::query()
            ->withCount('tenants')
            ->orderByDesc('tenants_count')
            ->get();

        $expiringTenants = Tenant::query()
            ->with('plan')
            ->where('subscription_status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(30)])
            ->orderBy('end_date')
            ->limit(10)
            ->get();

        return view('super_admin.analytics.index', compact(
            'stats',
            'tenantStatusCounts',
            'planDistribution',
            'expiringTenants'
        ));
    }
}
