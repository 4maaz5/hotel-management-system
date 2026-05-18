<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = DB::table('system_notifications');
        $departments = $this->scopeDepartmentsForUser(Department::with('branch'), $user)
            ->orderBy('branch_id')   // first group by branch
            ->orderBy('name')        // then sort by department name
            ->get();

        $this->scopeNotificationsForUser($query, $user);

        // Role-based filtering
        if (! $this->isGlobalSuperAdmin($user)) {
            $query->where(function ($q) use ($user) {
                // Notifications meant for everyone
                $q->where('recipient_type', 'all');

                // Department notifications already tenant/branch scoped above.
                $q->orWhere('recipient_type', 'department');

                // Branch-specific notifications (for managers)
                if ($user->branch_id) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->where('recipient_type', 'branch')
                            ->where('recipient_id', $user->branch_id);
                    });
                }

                // Employee-specific notifications
                $q->orWhere(function ($q3) use ($user) {
                    $q3->where('recipient_type', 'employee')
                        ->where('recipient_id', $user->id);
                });

                // Manager-specific notifications
                if ($user->hasRole('manager')) {
                    $q->orWhere(function ($q4) use ($user) {
                        $q4->where('recipient_type', 'manager')
                            ->where(function ($q5) use ($user) {
                                $q5->whereNull('recipient_id') // all managers
                                    ->orWhere('recipient_id', $user->id); // this manager
                            });
                    });
                }
            });
        }

        // Fetch notifications for table
        $notifications = $query->orderBy('created_at', 'desc')->get();

        // Clone the query for cards and paginate
        $notificationCards = (clone $query)->orderBy('created_at', 'desc')->paginate(10);

        return view('Admin.Backend.CustomNotification.index', compact('notifications', 'notificationCards', 'departments'));
    }

    private function scopeDepartmentsForUser($query, $user)
    {
        $tenantId = $this->tenantIdForUser($user);

        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        $branchIds = Branch::where('company_id', $tenantId)->pluck('id');

        return $query->where(function ($query) use ($tenantId, $branchIds) {
            $query->where('company_id', $tenantId)
                ->orWhereIn('branch_id', $branchIds);
        });
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id() ?: $user?->company_id;
    }

    private function scopeNotificationsForUser($query, $user): void
    {
        if ($this->isGlobalSuperAdmin($user)) {
            return;
        }

        $tenantId = $this->tenantIdForUser($user);
        $departmentIds = $this->scopeDepartmentsForUser(Department::query(), $user)->pluck('id');

        $query->where(function ($query) use ($tenantId, $departmentIds) {
            $query->whereIn('department_id', $departmentIds)
                ->orWhereIn('created_by', User::query()
                    ->where('company_id', $tenantId)
                    ->select('id'));
        });
    }

    private function isGlobalSuperAdmin($user): bool
    {
        return ! $this->tenantIdForUser($user) && $user->hasRole('super_admin');
    }
}
