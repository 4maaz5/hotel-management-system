@extends('layout.master')
@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.hr_management'))
<style>
    .program-page,
    .program-page * { box-sizing: border-box; }

    .program-page .page-header {
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .program-page .page-header h4 {
        font-size: 1.5rem;
        font-weight: 600;
        color: {{ $theme->dashboard_card_title_color }};
        margin: 0;
    }

    .program-page .page-header p {
        color: {{ $theme->dashboard_card_text_color }};
        opacity: 0.7;
        margin: 5px 0 0;
    }

    .program-page .settings-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    .program-page .setting-card {
        background: {{ $theme->dashboard_card_bg }};
        border: 2px solid {{ $theme->dashboard_card_border }};
        border-radius: 16px;
        padding: 35px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 180px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .program-page .setting-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .program-page .setting-card:hover::before { opacity: 1; }

    .program-page .setting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .program-page .setting-card .icon {
        width: 56px;
        height: 56px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }

    .program-page .setting-card .icon svg {
        width: 28px;
        height: 28px;
        fill: none;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .program-page .setting-card .title {
        color: {{ $theme->dashboard_card_title_color }};
        font-size: 15px;
        text-align: center;
        font-weight: 500;
    }

    @media (max-width: 1200px) { .program-page .settings-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .program-page .settings-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; } .program-page .setting-card { min-height: 150px; padding: 25px 15px; } }
    @media (max-width: 480px) { .program-page .settings-grid { grid-template-columns: 1fr; } }
</style>

@section('main')
    <div class="main-content">
    <div class="container-fluid program-page">

        <div class="settings-grid">
            @can('manage_employee')
            <a href="{{ route('dashboard.employee.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.all_employees') }}</div>
            </a>
            @endcan

            @can('manage_employee')
            <a href="{{ route('dashboard.shift.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#10b981">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.shifts') }}</div>
            </a>
            @endcan

            @can('manage_employee')
            <a href="{{ route('dashboard.employee.card') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #fef2f2, #fecaca);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444">
                        <rect x="2" y="4" width="20" height="16" rx="2" />
                        <line x1="12" y1="12" x2="18" y2="12" /><line x1="6" y1="12" x2="8" y2="12" />
                        <line x1="12" y1="16" x2="18" y2="16" /><line x1="6" y1="16" x2="8" y2="16" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.id_card_generator') }}</div>
            </a>
            @endcan

            @can('manage_attendance')
            <a href="{{ route('dashboard.employee.attendance.dashboard') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #fefce8, #fef08a);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#eab308">
                        <circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.attendance_dashboard') }}</div>
            </a>
            @endcan

            @can('manage_attendance')
            <a href="{{ route('dashboard.employee.attendance.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #ecfdf5, #a7f3d0);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#059669">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.mark_attendance') }}</div>
            </a>
            @endcan

            @can('manage_attendance')
            <a href="{{ route('dashboard.employee.leave.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #f5f3ff, #ddd6fe);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                        <path d="M8 14h.01M12 14h.01M16 14h.01" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.leaves') }}</div>
            </a>
            @endcan

            @can('manage_payroll')
            <a href="{{ route('dashboard.employee.payroll.dashboard') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #f0fdf4, #bbf7d0);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.payroll_dashboard') }}</div>
            </a>
            @endcan

            @can('manage_finance')
            <a href="{{ route('dashboard.finance.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #ecfeff, #cffafe);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0891b2">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.financial_dashboard') }}</div>
            </a>
            @endcan

            @can('manage_branch')
            <a href="{{ route('dashboard.branch.dashboard') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #f5f3ff, #c4b5fd);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#6d28d9">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.branch_dashboard') }}</div>
            </a>
            @endcan

            @can('manage_documents')
            <a href="{{ route('dashboard.document.employee.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #fff7ed, #fed7aa);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#ea580c">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.document_management') }}</div>
            </a>
            @endcan

            @can('manage_setting')
            <a href="{{ route('dashboard.setting.general.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #f1f5f9, #cbd5e1);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#475569">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.general_setting') }}</div>
            </a>
            @endcan

            @can('manage_warehouse')
            <a href="{{ route('dashboard.warehouse.index') }}" class="setting-card">
                <div class="icon" style="background: linear-gradient(135deg, #fefce8, #fde047);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#ca8a04">
                        <path d="M2 22h20" /><path d="M3 22V8l9-6 9 6v14" />
                        <path d="M10 22v-4h4v4" /><path d="M10 14h4" /><path d="M10 10h4" />
                    </svg>
                </div>
                <div class="title">{{ __('dashboard.warehouse') }}</div>
            </a>
            @endcan
        </div>
    </div>
    </div>
@endsection