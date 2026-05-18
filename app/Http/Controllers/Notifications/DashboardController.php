<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Mail\DepartmentNotificationMail;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SystemNotification;
use App\Models\User;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = $this->scopeNotificationsForUser(\DB::table('system_notifications'), $user);

        // Apply role-based filtering only if NOT super_admin
        if (! $this->isGlobalSuperAdmin($user)) {
            $query->where(function ($q) use ($user) {
                // Notifications meant for everyone
                $q->where('recipient_type', 'all');

                // Department notifications already tenant/branch scoped above.
                $q->orWhere('recipient_type', 'department');

                // Branch-specific notifications (for managers)
                if ($user->branch_id && $user->hasRole('manager')) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->where('recipient_type', 'manager')
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

        // Get all notifications for table
        $notifications = $query->orderBy('created_at', 'desc')->get();

        // Create separate variable for cards (with pagination)
        $notificationCardsQuery = clone $query; // clone to keep original query intact
        $notificationCards = $notificationCardsQuery->orderBy('created_at', 'desc')->paginate(10);

        // Calculate totals
        $totalNotifications = $notifications->count();
        $failedNotifications = $notifications->where('status', 'failed')->count();

        $notificationsByType = $notifications->groupBy('type')->map(function ($group) {
            return $group->count();
        });

        $emailNotifications = $notificationsByType->get('email', 0);
        $smsNotifications = $notificationsByType->get('sms', 0);

        return view('Admin.Backend.Notifications.dashboard', compact(
            'notifications',
            'notificationCards',
            'totalNotifications',
            'failedNotifications',
            'notificationsByType',
            'emailNotifications',
            'smsNotifications'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sms,email,system',
            'message' => 'required|string',
            // 'recipient_type' => 'required|string',
            'status' => 'required|in:pending,sent,failed',
            'department_ids' => 'required|array',
            'department_ids.*' => [
                'integer',
                Rule::exists('departments', 'id')->where(
                    fn ($query) => $this->scopeDepartmentsForUser($query, $request->user())
                ),
            ],
        ]);

        $user = $request->user();

        // Fetch employees in the selected departments
        $employees = $this->scopeEmployeesForUser(
            Employee::whereIn('department_id', $request->department_ids),
            $user
        )->get();
        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no_employees_found_in_selected_departments'),
            ], 422);
        }

        // Get their linked user accounts
        $recipients = User::with('employee')
            ->whereIn('id', $employees->pluck('user_id')->filter())
            ->when($this->tenantIdForUser($user), fn ($query, $tenantId) => $query->where('company_id', $tenantId))
            // ->role('employee')
            ->get();
        if ($recipients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no_employees_found_in_selected_departments'),
            ], 422);
        }

        foreach ($recipients as $recipient) {

            // 1. Insert into DB
            $notificationId = DB::table('system_notifications')->insertGetId([
                'type' => $request->type,
                'message' => $request->message,
                'recipient_type' => 'department',
                'recipient_id' => null,
                'status' => $request->status,
                'department_id' => $recipient->employee?->department_id,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. If type is email → send email to user
            if ($request->type === 'email') {
                Mail::to($recipient->email)->send(
                    new DepartmentNotificationMail($request->message)
                );
            }

        }

        return response()->json([
            'success' => true,
            'message' => __('messages.notification_sent_successfully'),
        ]);
    }

    public function unreadForCurrentUser()
    {
        $user = Auth::user();

        return $this->fetchForUser(10)
            ->filter(function ($notification) use ($user) {
                return ! $notification->reads()
                    ->where('user_id', $user->id)
                    ->exists();
            })
            ->values();
    }

    public function fetchForUser($limit = 50)
    {
        $user = Auth::user();

        // Exclude the creator's own notifications
        $baseQuery = SystemNotification::query()
            ->where('status', 'sent')
            ->where('created_by', '!=', $user->id)
            ->where('recipient_type', 'department');

        // IF USER IS EMPLOYEE
        if ($user->hasRole('employee')) {

            $departmentId = optional($user->employee)->department_id;

            return $baseQuery
                ->where('department_id', $departmentId)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }

        // IF USER IS MANAGER
        if ($user->hasRole('manager')) {

            if ($user->branch_id) {
                // Manager must see notifications of departments in his branch
                $departmentIds = \App\Models\Department::where('branch_id', $user->branch_id)
                    ->pluck('id');
            } else {
                $departmentIds = [];
            }

            return $baseQuery
                ->whereIn('department_id', $departmentIds)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }

        // SUPER ADMIN → sees all except own
        return $baseQuery
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    // protected function fetchForUser($user)
    // {
    //     return \App\Models\SystemNotification::whereIn('type', ['System', 'email'])
    //         ->where(function ($q) use ($user) {

    //             // Employee-specific notifications
    //             $q->where('recipient_type', 'employee')
    //                 ->where('recipient_id', $user->id)

    //             // Manager-specific notifications
    //                 ->orWhere(function ($q2) use ($user) {
    //                     if ($user->hasRole('manager')) {
    //                         $q2->where('recipient_type', 'manager')
    //                             ->where(function ($q3) use ($user) {
    //                                 $q3->whereNull('recipient_id') // all managers
    //                                     ->orWhere('recipient_id', $user->id); // specific manager
    //                             });
    //                     }
    //                 })

    //             // Super Admin-specific notifications
    //                 ->orWhere(function ($q4) use ($user) {
    //                     if ($user->hasRole('super_admin')) {
    //                         $q4->where('recipient_type', 'super_admin')
    //                             ->where(function ($q5) use ($user) {
    //                                 $q5->whereNull('recipient_id') // all super admins
    //                                     ->orWhere('recipient_id', $user->id); // specific super admin
    //                             });
    //                     }
    //                 });

    //         })
    //         ->orderBy('created_at', 'desc');
    // }

    public function markAllRead(Request $request)
    {
        $user = $request->user();

        // Get all unread notifications for the user
        $unreadIds = \DB::table('system_notifications')
            ->leftJoin('system_notification_reads', function ($join) use ($user) {
                $join->on('system_notifications.id', '=', 'system_notification_reads.notification_id')
                    ->where('system_notification_reads.user_id', $user->id);
            })
            ->whereNull('system_notification_reads.id')
            ->pluck('system_notifications.id');

        foreach ($unreadIds as $id) {
            \DB::table('system_notification_reads')->updateOrInsert(
                ['notification_id' => $id, 'user_id' => $user->id],
                ['read_at' => Carbon::now()]
            );
        }

        return response()->json(['success' => true]);
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'type' => 'required|in:SMS,Email,System',
    //         'message' => 'required|string',
    //         'recipient_type' => 'nullable|string',
    //         'status' => 'required|in:pending,sent,failed',
    //         'scheduled_date' => 'nullable|date',
    //         'scheduled_time' => 'nullable',
    //     ]);

    //     // $scheduled_at = null;
    //     // if ($request->scheduled_date && $request->scheduled_time) {
    //     //     $scheduled_at = Carbon::parse($request->scheduled_date.' '.$request->scheduled_time);
    //     // }

    //     $notification = \DB::table('system_notifications')->where('id', $id)->first();
    //     \DB::table('system_notifications')->where('id', $id)->update([
    //         'type' => $request->type,
    //         'message' => $request->message,
    //         'status' => $request->status,
    //         'scheduled_at' => '2025-11-12 05:53:38', // $scheduled_at,
    //         'updated_at' => now(),
    //     ]);

    //     $updatedNotification = \DB::table('system_notifications')->where('id', $id)->first();

    //     return response()->json([
    //         'success' => true,
    //         'message' => __('messages.notification_updated_successfully'),
    //         'notification' => $updatedNotification,
    //     ]);
    // }

    public function destroy($id)
    {
        $notification = $this->scopeNotificationsForUser(SystemNotification::query(), request()->user())->findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => __('messages.notification_deleted_successfully'),
        ]);
    }

    public function viewAll()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            // Super admin sees everything
            $notifications = \DB::table('system_notifications')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user->hasRole('manager') && $user->branch_id) {
            // Manager should see:

            $notifications = \DB::table('system_notifications')
                ->leftJoin('users as recipients', 'system_notifications.recipient_id', '=', 'recipients.id')
                ->where('system_notifications.status', 'sent')
                ->where(function ($query) use ($user) {
                    $query
                        // Broadcast notifications to all managers
                        ->where(function ($q) {
                            $q->where('system_notifications.recipient_type', 'manager')
                                ->whereNull('system_notifications.recipient_id');
                        })
                        // Notifications directly sent to this manager
                        ->orWhere(function ($q) use ($user) {
                            $q->where('system_notifications.recipient_type', 'manager')
                                ->where('system_notifications.recipient_id', $user->id);
                        })
                        // Notifications meant for employees in this manager's branch
                        ->orWhere(function ($q) use ($user) {
                            $q->where('system_notifications.recipient_type', 'employee')
                                ->where('recipients.branch_id', $user->branch_id);
                        });
                })
                ->select('system_notifications.*')
                ->orderBy('system_notifications.created_at', 'desc')
                ->get();

        } elseif ($user->branch_id) {
            // Employee — only own notifications
            $notifications = \DB::table('system_notifications')
                ->where('recipient_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Owner (no branch_id): show all notifications
            $notifications = \DB::table('system_notifications')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('Admin.Backend.CustomNotification.viewAll', compact('notifications'));
    }

    public function filter(Request $request)
    {
        $query = $this->scopeNotificationsForUser(SystemNotification::query(), $request->user());

        //  Filter by Type (sms, email, system)
        if ($request->type) {
            $query->where('type', $request->type);
        }

        //  Filter by Status (pending, sent, failed)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Execute Query
        $notifications = $query->get();

        // Return table rows as HTML
        $html = view('Admin.Backend.partials.notification_rows', compact('notifications'))->render();

        return response()->json([
            'html' => $html,
        ]);
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

    private function scopeEmployeesForUser($query, $user)
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

    private function scopeNotificationsForUser($query, $user)
    {
        $tenantId = $this->tenantIdForUser($user);

        if ($this->isGlobalSuperAdmin($user)) {
            return $query;
        }

        $departmentIds = $this->scopeDepartmentsForUser(Department::query(), $user)->pluck('id');

        return $query->where(function ($query) use ($tenantId, $departmentIds) {
            $query->whereIn('department_id', $departmentIds)
                ->orWhereIn('created_by', User::query()
                    ->where('company_id', $tenantId)
                    ->select('id'));
        });
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id() ?: $user?->company_id;
    }

    private function isGlobalSuperAdmin($user): bool
    {
        return ! $this->tenantIdForUser($user) && $user->hasRole('super_admin');
    }
}
