<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $hasActivityTable = Schema::hasTable('user_activity_logs');

        $filters = [
            'tenant_id' => $request->query('tenant_id'),
            'module' => $request->query('module'),
            'action' => $request->query('action'),
        ];

        $activityLogs = $hasActivityTable
            ? UserActivityLog::query()
                ->with(['user', 'tenant'])
                ->when($filters['tenant_id'], fn ($query, $tenantId) => $query->where('company_id', $tenantId))
                ->when($filters['module'], fn ($query, $module) => $query->where('module', $module))
                ->when($filters['action'], fn ($query, $action) => $query->where('action', $action))
                ->latest()
                ->paginate(20)
                ->withQueryString()
            : collect();

        $modules = $hasActivityTable
            ? UserActivityLog::query()->select('module')->distinct()->orderBy('module')->pluck('module')
            : collect();

        $actions = $hasActivityTable
            ? UserActivityLog::query()->select('action')->distinct()->orderBy('action')->pluck('action')
            : collect();

        $stats = [
            'total' => $hasActivityTable ? UserActivityLog::count() : 0,
            'today' => $hasActivityTable ? UserActivityLog::whereDate('created_at', today())->count() : 0,
            'modules' => $modules->count(),
            'users' => $hasActivityTable ? UserActivityLog::whereNotNull('user_id')->distinct('user_id')->count('user_id') : 0,
        ];

        $tenants = Tenant::query()->orderBy('name')->get(['id', 'name']);

        return view('super_admin.activity.index', compact(
            'activityLogs',
            'actions',
            'filters',
            'hasActivityTable',
            'modules',
            'stats',
            'tenants'
        ));
    }
}
