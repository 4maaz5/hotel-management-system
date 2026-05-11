<?php

namespace App\Http\Controllers;

use App\Models\NightAudit;
use App\Models\Notification;
use App\Models\SmsLog;
use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $this->normalizeTab($request->query('tab'));

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'user_id' => $request->query('user_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'sms_status' => $request->query('sms_status'),
            'delivery_mode' => $request->query('delivery_mode'),
            'audit_status' => $request->query('audit_status'),
            'notification_type' => $request->query('notification_type'),
            'notification_read' => $request->query('notification_read'),
            'activity_module' => $request->query('activity_module'),
            'activity_action' => $request->query('activity_action'),
        ];

        $smsLogs = $this->buildSmsLogsQuery($filters)
            ->paginate(12, ['*'], 'sms_page')
            ->withQueryString();

        $nightAudits = $this->buildNightAuditsQuery($filters)
            ->paginate(12, ['*'], 'night_audits_page')
            ->withQueryString();

        $notifications = $this->buildNotificationsQuery($filters)
            ->paginate(12, ['*'], 'notifications_page')
            ->withQueryString();

        $activityLogs = $this->buildUserActivityLogsQuery($filters)
            ->paginate(12, ['*'], 'activity_page')
            ->withQueryString();

        return view('admin.logs.index', [
            'tab' => $tab,
            'filters' => $filters,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'notificationTypes' => Notification::query()
                ->select('type')
                ->distinct()
                ->orderBy('type')
                ->pluck('type'),
            'activityModules' => UserActivityLog::query()
                ->select('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module'),
            'activityActions' => UserActivityLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
            'summary' => [
                'sms_logs' => SmsLog::count(),
                'failed_sms_logs' => SmsLog::where('status', 'failed')->count(),
                'night_audits' => NightAudit::count(),
                'pending_night_audits' => NightAudit::where('status', 'pending')->count(),
                'notifications' => Notification::count(),
                'unread_notifications' => Notification::whereNull('read_at')->count(),
                'user_activity_logs' => UserActivityLog::count(),
            ],
            'smsLogs' => $smsLogs,
            'nightAudits' => $nightAudits,
            'notifications' => $notifications,
            'activityLogs' => $activityLogs,
        ]);
    }

    private function normalizeTab(?string $tab): string
    {
        return in_array($tab, ['sms', 'night_audits', 'notifications', 'user_activity'], true) ? $tab : 'user_activity';
    }

    private function buildSmsLogsQuery(array $filters): Builder
    {
        return SmsLog::query()
            ->with(['requestedBy', 'guest'])
            ->when($filters['q'], function (Builder $query, string $search) {
                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery->where('phone', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%")
                        ->orWhere('message_preview', 'like', "%{$search}%");
                });
            })
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('requested_by', $userId))
            ->when($filters['date_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['sms_status'], fn (Builder $query, $status) => $query->where('status', $status))
            ->when($filters['delivery_mode'], fn (Builder $query, $mode) => $query->where('delivery_mode', $mode))
            ->latest();
    }

    private function buildNightAuditsQuery(array $filters): Builder
    {
        return NightAudit::query()
            ->with('user')
            ->when($filters['q'], function (Builder $query, string $search) {
                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery->where('notes', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'], fn (Builder $query, $date) => $query->whereDate('start_date_time', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, $date) => $query->whereDate('start_date_time', '<=', $date))
            ->when($filters['audit_status'], fn (Builder $query, $status) => $query->where('status', $status))
            ->latest();
    }

    private function buildNotificationsQuery(array $filters): Builder
    {
        return Notification::query()
            ->with('user')
            ->when($filters['q'], function (Builder $query, string $search) {
                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('title_ar', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('message_ar', 'like', "%{$search}%");
                });
            })
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['notification_type'], fn (Builder $query, $type) => $query->where('type', $type))
            ->when($filters['notification_read'] === 'read', fn (Builder $query) => $query->whereNotNull('read_at'))
            ->when($filters['notification_read'] === 'unread', fn (Builder $query) => $query->whereNull('read_at'))
            ->latest();
    }

    private function buildUserActivityLogsQuery(array $filters): Builder
    {
        return UserActivityLog::query()
            ->with(['user', 'property'])
            ->when($filters['q'], function (Builder $query, string $search) {
                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery->where('description', 'like', "%{$search}%")
                        ->orWhere('subject_reference', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%");
                });
            })
            ->when($filters['user_id'], fn (Builder $query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['activity_module'], fn (Builder $query, $module) => $query->where('module', $module))
            ->when($filters['activity_action'], fn (Builder $query, $action) => $query->where('action', $action))
            ->latest();
    }
}
