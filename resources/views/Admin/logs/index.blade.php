@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $isArabic = app()->getLocale() === 'ar';
    $t = fn(string $ar, string $en): string => $isArabic ? $ar : $en;
@endphp

@section('title', 'Logs')

<style>
    .logs-page .hero-card,
    .logs-page .summary-card,
    .logs-page .content-card {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.05);
    }

    .logs-page .hero-card {
        background:
            radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 32%),
            linear-gradient(135deg, rgba(37, 99, 235, 0.06), rgba(248, 250, 252, 1));
    }

    .logs-page .summary-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
    }

    .logs-page .log-tabs .nav-link {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 999px;
        color: #0f172a;
        font-weight: 600;
        padding: 10px 18px;
        background: #fff;
    }

    .logs-page .log-tabs .nav-link.active {
        background: {{ $theme->button_primary_color }};
        border-color: {{ $theme->button_primary_color }};
        color: #fff;
    }

    .logs-page .log-tabs .nav-item:last-child {
        display: none;
    }

    .logs-page .filter-shell {
        border: 1px solid {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 18px;
        background: #f8fafc;
    }

    .logs-page .table td,
    .logs-page .table th {
        vertical-align: middle;
    }

    .logs-page .empty-state {
        border: 1px dashed {{ $theme->card_border_color ?: '#dbe2ea' }};
        border-radius: 18px;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
        padding: 28px;
    }

    .logs-page .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
    }
</style>

@section('content')
    <main class="bg-white p-3 p-lg-4" style="border-radius: 15px;">
        <section class="logs-page">
            <div class="text-muted small text-uppercase mb-2">{{ __('dashboard.logs') }}</div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="hero-card p-4 h-100">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <h2 class="mb-2">{{ $t('سجل النظام', 'System Logs') }}</h2>
                                <p class="text-muted mb-0">
                                    {{ $t('هذه الصفحة تعرض الآن سجلات الرسائل النصية، التدقيق الليلي، والإشعارات من قاعدة البيانات مباشرة بدون الاعتماد على laravel.log.', 'This page now shows SMS logs, night audits, and notifications directly from the database without relying on laravel.log.') }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="meta-pill">{{ $t('٣ مصادر حية', '3 live sources') }}</span>
                                <span class="meta-pill">{{ $t('قابلة للفلترة', 'Filterable') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card p-4 h-100">
                        <div class="small text-muted mb-2">{{ $t('النظرة العامة', 'Overview') }}</div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('سجلات الرسائل', 'SMS logs') }}</span>
                            <strong>{{ $summary['sms_logs'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('فشل الرسائل', 'Failed SMS') }}</span>
                            <strong>{{ $summary['failed_sms_logs'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('التدقيق الليلي', 'Night audits') }}</span>
                            <strong>{{ $summary['night_audits'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('تدقيقات معلقة', 'Pending audits') }}</span>
                            <strong>{{ $summary['pending_night_audits'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ $t('الإشعارات', 'Notifications') }}</span>
                            <strong>{{ $summary['notifications'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ $t('غير مقروءة', 'Unread') }}</span>
                            <strong>{{ $summary['unread_notifications'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-muted">{{ $t('Ù†Ø´Ø§Ø· Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…', 'User activity') }}</span>
                            <strong>{{ $summary['user_activity_logs'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#7c3aed;"><i class="fas fa-user-clock"></i></div>
                        <div>
                            <div class="small text-muted">{{ $t('Ù†Ø´Ø§Ø· Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…', 'User Activity') }}</div>
                            <div class="h4 mb-1">{{ $summary['user_activity_logs'] }}</div>
                            <div class="small text-muted">{{ $t('ØªØºÙŠÙŠØ±Ø§Øª Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…ÙŠÙ† Ø¹Ù„Ù‰ Ø§Ù„Ù†Ø¸Ø§Ù…', 'Tracked business actions by users') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#2563eb;"><i class="fas fa-sms"></i></div>
                        <div>
                            <div class="small text-muted">{{ $t('الرسائل النصية', 'SMS Logs') }}</div>
                            <div class="h4 mb-1">{{ $summary['sms_logs'] }}</div>
                            <div class="small text-muted">{{ $t('محاولات الإرسال المحفوظة', 'Stored delivery attempts') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#0f766e;"><i class="fas fa-moon"></i></div>
                        <div>
                            <div class="small text-muted">{{ $t('التدقيق الليلي', 'Night Audits') }}</div>
                            <div class="h4 mb-1">{{ $summary['night_audits'] }}</div>
                            <div class="small text-muted">{{ $t('حالة الإغلاق والمراجعة الليلية', 'Night close and review history') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="summary-card p-3 h-100 d-flex gap-3 align-items-start">
                        <div class="summary-icon" style="background:#ea580c;"><i class="fas fa-bell"></i></div>
                        <div>
                            <div class="small text-muted">{{ $t('الإشعارات', 'Notifications') }}</div>
                            <div class="h4 mb-1">{{ $summary['notifications'] }}</div>
                            <div class="small text-muted">{{ $t('تنبيهات النظام للمستخدمين', 'System alerts for users') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card p-4">
                <ul class="nav log-tabs border-0 gap-2 mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'user_activity' ? 'active' : '' }}"
                            href="{{ route('dashboard.logs.index', ['tab' => 'user_activity']) }}">
                            {{ $t('Ã™â€ Ã˜Â´Ã˜Â§Ã˜Â· Ã˜Â§Ã™â€žÃ™â€¦Ã˜Â³Ã˜ÂªÃ˜Â®Ã˜Â¯Ã™â€¦', 'User Activity') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'sms' ? 'active' : '' }}"
                            href="{{ route('dashboard.logs.index', ['tab' => 'sms']) }}">
                            {{ $t('سجل الرسائل', 'SMS Logs') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'night_audits' ? 'active' : '' }}"
                            href="{{ route('dashboard.logs.index', ['tab' => 'night_audits']) }}">
                            {{ $t('سجل التدقيق الليلي', 'Night Audit Logs') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'notifications' ? 'active' : '' }}"
                            href="{{ route('dashboard.logs.index', ['tab' => 'notifications']) }}">
                            {{ $t('سجل الإشعارات', 'Notification Logs') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'user_activity' ? 'active' : '' }}"
                            href="{{ route('dashboard.logs.index', ['tab' => 'user_activity']) }}">
                            {{ $t('Ù†Ø´Ø§Ø· Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…', 'User Activity') }}
                        </a>
                    </li>
                </ul>

                <form method="GET" action="{{ route('dashboard.logs.index') }}" class="filter-shell p-3 mb-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3">
                            <label class="form-label">{{ $t('بحث', 'Search') }}</label>
                            <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control"
                                placeholder="{{ $t('ابحث في السجل الحالي', 'Search current log') }}">
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">{{ __('dashboard.user') }}</label>
                            <select name="user_id" class="form-select">
                                <option value="">{{ __('dashboard.all') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (string) $filters['user_id'] === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">{{ __('dashboard.date_from') }}</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label">{{ __('dashboard.date_to') }}</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                        </div>

                        @if ($tab === 'sms')
                            <div class="col-lg-1">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="sms_status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="sent" {{ $filters['sms_status'] === 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="simulated" {{ $filters['sms_status'] === 'simulated' ? 'selected' : '' }}>Simulated</option>
                                    <option value="failed" {{ $filters['sms_status'] === 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">{{ $t('وضع الإرسال', 'Delivery Mode') }}</label>
                                <select name="delivery_mode" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="simulation" {{ $filters['delivery_mode'] === 'simulation' ? 'selected' : '' }}>Simulation</option>
                                    <option value="gateway" {{ $filters['delivery_mode'] === 'gateway' ? 'selected' : '' }}>Gateway</option>
                                </select>
                            </div>
                        @elseif ($tab === 'night_audits')
                            <div class="col-lg-3">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="audit_status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="pending" {{ $filters['audit_status'] === 'pending' ? 'selected' : '' }}>{{ __('dashboard.pending') }}</option>
                                    <option value="completed" {{ $filters['audit_status'] === 'completed' ? 'selected' : '' }}>{{ __('dashboard.completed') }}</option>
                                    <option value="failed" {{ $filters['audit_status'] === 'failed' ? 'selected' : '' }}>{{ __('dashboard.failed') }}</option>
                                </select>
                            </div>
                        @elseif ($tab === 'notifications')
                            <div class="col-lg-2">
                                <label class="form-label">{{ $t('نوع الإشعار', 'Notification Type') }}</label>
                                <select name="notification_type" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($notificationTypes as $notificationType)
                                        <option value="{{ $notificationType }}" {{ $filters['notification_type'] === $notificationType ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $notificationType)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">{{ $t('القراءة', 'Read State') }}</label>
                                <select name="notification_read" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="unread" {{ $filters['notification_read'] === 'unread' ? 'selected' : '' }}>{{ $t('غير مقروء', 'Unread') }}</option>
                                    <option value="read" {{ $filters['notification_read'] === 'read' ? 'selected' : '' }}>{{ $t('مقروء', 'Read') }}</option>
                                </select>
                            </div>
                        @else
                            <div class="col-lg-2">
                                <label class="form-label">{{ $t('Ø§Ù„ÙˆØ­Ø¯Ø©', 'Module') }}</label>
                                <select name="activity_module" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($activityModules as $activityModule)
                                        <option value="{{ $activityModule }}" {{ $filters['activity_module'] === $activityModule ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $activityModule)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">{{ $t('Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡', 'Action') }}</label>
                                <select name="activity_action" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($activityActions as $activityAction)
                                        <option value="{{ $activityAction }}" {{ $filters['activity_action'] === $activityAction ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $activityAction)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-2"></i>{{ $t('تصفية', 'Filter') }}
                            </button>
                            <a href="{{ route('dashboard.logs.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary">
                                {{ $t('إعادة', 'Reset') }}
                            </a>
                        </div>
                    </div>
                </form>

                @if ($tab === 'sms')
                    @if ($smsLogs->isEmpty())
                        <div class="empty-state">
                            <div class="fw-semibold mb-2">{{ $t('لا توجد سجلات رسائل حالياً', 'No SMS logs found right now') }}</div>
                            <div class="small">{{ $t('ستظهر هنا محاولات الإرسال اليدوي أو التلقائي بمجرد تنفيذها.', 'Manual or automatic SMS delivery attempts will appear here once they run.') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ $t('التاريخ', 'Date') }}</th>
                                        <th>{{ __('dashboard.status') }}</th>
                                        <th>{{ $t('الوضع', 'Mode') }}</th>
                                        <th>{{ $t('المستلم', 'Recipient') }}</th>
                                        <th>{{ __('dashboard.mobile_number') }}</th>
                                        <th>{{ __('dashboard.user') }}</th>
                                        <th>{{ __('dashboard.message_body') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smsLogs as $smsLog)
                                        <tr>
                                            <td>{{ $smsLog->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $smsLog->status === 'failed' ? 'bg-danger' : ($smsLog->status === 'simulated' ? 'bg-warning text-dark' : 'bg-success') }}">
                                                    {{ ucfirst($smsLog->status) }}
                                                </span>
                                            </td>
                                            <td>{{ ucfirst($smsLog->delivery_mode) }}</td>
                                            <td>{{ $smsLog->recipient_name ?: ($smsLog->guest?->full_name ?: '-') }}</td>
                                            <td>{{ $smsLog->phone }}</td>
                                            <td>{{ $smsLog->requestedBy?->name ?: '-' }}</td>
                                            <td>
                                                <div>{{ \Illuminate\Support\Str::limit($smsLog->message_preview, 100) }}</div>
                                                @if ($smsLog->error_message)
                                                    <div class="small text-danger mt-1">{{ \Illuminate\Support\Str::limit($smsLog->error_message, 100) }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $smsLogs->links() }}
                        </div>
                    @endif
                @elseif ($tab === 'night_audits')
                    @if ($nightAudits->isEmpty())
                        <div class="empty-state">
                            <div class="fw-semibold mb-2">{{ $t('لا توجد سجلات تدقيق ليلية حالياً', 'No night audit logs found right now') }}</div>
                            <div class="small">{{ $t('ستظهر هنا نتائج وتشغيلات التدقيق الليلي بمجرد توفرها.', 'Night audit runs and results will appear here once available.') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('dashboard.start_date_time') }}</th>
                                        <th>{{ __('dashboard.end_date_time') }}</th>
                                        <th>{{ __('dashboard.status') }}</th>
                                        <th>{{ __('dashboard.user') }}</th>
                                        <th>{{ __('dashboard.period_date_from') }}</th>
                                        <th>{{ __('dashboard.period_date_to') }}</th>
                                        <th>{{ __('dashboard.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nightAudits as $audit)
                                        <tr>
                                            <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                                            <td>{{ $audit->end_date_time?->format('Y-m-d H:i') ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $audit->status === 'completed' ? 'bg-success' : ($audit->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ ucfirst($audit->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $audit->user?->name ?: '-' }}</td>
                                            <td>{{ $audit->period_date_from->format('Y-m-d') }}</td>
                                            <td>{{ $audit->period_date_to->format('Y-m-d') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($audit->notes ?: '-', 90) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $nightAudits->links() }}
                        </div>
                    @endif
                @elseif ($tab === 'notifications')
                    @if ($notifications->isEmpty())
                        <div class="empty-state">
                            <div class="fw-semibold mb-2">{{ $t('لا توجد إشعارات حالياً', 'No notification logs found right now') }}</div>
                            <div class="small">{{ $t('ستظهر هنا إشعارات النظام المسجلة للمستخدمين.', 'System notifications recorded for users will appear here.') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ $t('التاريخ', 'Date') }}</th>
                                        <th>{{ $t('النوع', 'Type') }}</th>
                                        <th>{{ $t('العنوان', 'Title') }}</th>
                                        <th>{{ $t('الرسالة', 'Message') }}</th>
                                        <th>{{ __('dashboard.user') }}</th>
                                        <th>{{ $t('الحالة', 'Read State') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</td>
                                            <td>{{ $isArabic ? $notification->title_ar : $notification->title }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($isArabic ? $notification->message_ar : $notification->message, 100) }}</td>
                                            <td>{{ $notification->user?->name ?: '-' }}</td>
                                            <td>
                                                <span class="badge {{ $notification->read_at ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $notification->read_at ? $t('مقروء', 'Read') : $t('غير مقروء', 'Unread') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @else
                    @if ($activityLogs->isEmpty())
                        <div class="empty-state">
                            <div class="fw-semibold mb-2">{{ $t('Ù„Ø§ ØªÙˆØ¬Ø¯ Ø³Ø¬Ù„Ø§Øª Ù†Ø´Ø§Ø· Ø­Ø§Ù„ÙŠØ§Ù‹', 'No user activity logs found right now') }}</div>
                            <div class="small">{{ $t('Ø³ØªØ¸Ù‡Ø± Ù‡Ù†Ø§ Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…ÙŠÙ† Ø§Ù„Ù…Ù‡Ù…Ø© Ù…Ø«Ù„ Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø­Ø¬ÙˆØ²Ø§Øª ÙˆØªØ¹Ø¯ÙŠÙ„ Ø§Ù„Ø£Ø³Ø¹Ø§Ø± ÙˆØ¥Ù„ØºØ§Ø¡ Ø§Ù„Ø¹Ù…Ù„ÙŠØ§Øª.', 'Important user actions like reservations, pricing updates, and cancellations will appear here.') }}</div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ $t('Ø§Ù„ØªØ§Ø±ÙŠØ®', 'Date') }}</th>
                                        <th>{{ __('dashboard.user') }}</th>
                                        <th>{{ $t('Ø§Ù„ÙˆØ­Ø¯Ø©', 'Module') }}</th>
                                        <th>{{ $t('Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡', 'Action') }}</th>
                                        <th>{{ $t('Ø§Ù„Ù…Ø±Ø¬Ø¹', 'Reference') }}</th>
                                        <th>{{ $t('Ø§Ù„ØªÙØ§ØµÙŠÙ„', 'Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activityLogs as $activityLog)
                                        <tr>
                                            <td>{{ $activityLog->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $activityLog->user?->name ?: '-' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $activityLog->module)) }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}
                                                </span>
                                            </td>
                                            <td>{{ $activityLog->subject_reference ?: '-' }}</td>
                                            <td>
                                                <div>{{ $activityLog->description }}</div>
                                                @if ($activityLog->property)
                                                    <div class="small text-muted mt-1">
                                                        {{ $t('Ø§Ù„ÙØ±Ø¹', 'Property') }}: {{ $activityLog->property->property_name_en }}
                                                    </div>
                                                @endif
                                                @if (! empty($activityLog->metadata['changed_fields']))
                                                    <div class="small text-muted mt-1">
                                                        {{ $t('Ø§Ù„Ø­Ù‚ÙˆÙ„ Ø§Ù„Ù…ØªØºÙŠØ±Ø©', 'Changed fields') }}:
                                                        {{ collect($activityLog->metadata['changed_fields'])->map(fn ($field) => str_replace('_', ' ', $field))->implode(', ') }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $activityLogs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </main>
@endsection
