@extends('layouts.app')

<style>
    .dashboard-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon.primary { background: linear-gradient(135deg, #6366f1, #818cf8); color: white; }
    .stat-icon.success { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
    .stat-icon.danger { background: linear-gradient(135deg, #ef4444, #f87171); color: white; }
    .stat-icon.info { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
    .stat-icon.dark { background: linear-gradient(135deg, #374151, #4b5563); color: white; }

    .section-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        background: #f8fafc;
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .section-header h6 {
        margin: 0;
        color: #1e293b;
        font-weight: 600;
        font-size: 15px;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active { background: #d1fae5; color: #059669; }
    .status-badge.inactive { background: #f3f4f6; color: #6b7280; }
    .status-badge.pending { background: #fef3c7; color: #d97706; }

    .progress-thin {
        height: 10px;
        border-radius: 5px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .progress-thin .progress-bar {
        border-radius: 5px;
    }

    .hk-status {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .hk-status:hover {
        background: #f1f5f9;
    }

    .hk-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .quick-action-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }

    .quick-action-btn:hover i {
        color: white;
    }

    .quick-action-btn:hover span {
        color: white;
    }

    .quick-action-btn i {
        font-size: 28px;
        margin-bottom: 10px;
        color: #6366f1;
        transition: color 0.3s ease;
    }

    .quick-action-btn span {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        transition: color 0.3s ease;
    }

    .dashboard-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .live-badge {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .big-number {
        font-size: 42px;
        font-weight: 700;
        line-height: 1;
    }

    .table-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .table-card .table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        padding: 14px 16px;
    }

    .table-card .table td {
        padding: 14px 16px;
        vertical-align: middle;
        color: #334155;
    }

    .table-card .table tbody tr:hover {
        background: #f8fafc;
    }

    .channel-logo {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        object-fit: cover;
    }
</style>

@section('content')
    <div class="container-fluid p-4">

        <!-- Dashboard Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1 text-white">{{ __('dashboard.dashboard') }}</h3>
                <p class="mb-0 text-white-50" style="opacity: 0.8;">{{ __('dashboard.you_can_view_and_manage_the_whole_property_at_a_glance') }}</p>
            </div>
            <div class="text-end text-white">
                <div class="fw-bold" style="font-size: 20px;">{{ date('h:i A') }}</div>
                <div style="opacity: 0.8;">{{ date('l, d M Y') }}</div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon primary me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('dashboard.todays_arrivals') }}</div>
                        <div class="fw-bold" style="font-size: 28px; color: #6366f1;">{{ $todaysArrivals ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon success me-3">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('dashboard.todays_departures') }}</div>
                        <div class="fw-bold" style="font-size: 28px; color: #10b981;">{{ $todaysDepartures ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon warning me-3">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('dashboard.in_house') }}</div>
                        <div class="fw-bold" style="font-size: 28px; color: #f59e0b;">{{ $inHouse ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon info me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('dashboard.checked_in') }}</div>
                        <div class="fw-bold" style="font-size: 28px; color: #3b82f6;">{{ $checkedIn ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row g-4 mb-4">

            <!-- Reservation Status -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h6><i class="fas fa-chart-pie me-2" style="color: #6366f1;"></i>{{ __('dashboard.reservation_status') }}</h6>
                        <span class="live-badge"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>{{ __('dashboard.live') }}</span>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between mb-3 p-3" style="background: #f0fdf4; border-radius: 10px;">
                            <span class="text-muted fw-medium">{{ __('dashboard.on_arrival') }}</span>
                            <span class="fw-bold" style="color: #10b981;">{{ $onArrival ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 p-3" style="background: #eff6ff; border-radius: 10px;">
                            <span class="text-muted fw-medium">{{ __('dashboard.checked_in') }}</span>
                            <span class="fw-bold" style="color: #3b82f6;">{{ $checkedInCount ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 p-3" style="background: #fffbeb; border-radius: 10px;">
                            <span class="text-muted fw-medium">{{ __('dashboard.on_departure') }}</span>
                            <span class="fw-bold" style="color: #f59e0b;">{{ $onDeparture ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 p-3" style="background: #fef2f2; border-radius: 10px;">
                            <span class="text-muted fw-medium">{{ __('dashboard.checked_out') }}</span>
                            <span class="fw-bold" style="color: #ef4444;">{{ $checkedOut ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between p-3" style="background: #f8fafc; border-radius: 10px;">
                            <span class="text-muted fw-medium">{{ __('dashboard.in_house') }}</span>
                            <span class="fw-bold" style="color: #1e293b;">{{ $inHouse ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <h6><i class="fas fa-wallet me-2" style="color: #10b981;"></i>{{ __('dashboard.financial_summary') }}</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-3 text-center" style="background: #f0fdf4; border-radius: 12px;">
                                    <div class="fw-bold" style="font-size: 22px; color: #10b981;">+{{ number_format($totalRevenue ?? 0) }}</div>
                                    <small class="text-muted">{{ __('dashboard.sar') }}</small>
                                    <div class="mt-1" style="color: #059669; font-size: 12px;">{{ __('dashboard.total_revenue') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 text-center" style="background: #fef2f2; border-radius: 12px;">
                                    <div class="fw-bold" style="font-size: 22px; color: #ef4444;">-{{ number_format($totalExpense ?? 0) }}</div>
                                    <small class="text-muted">{{ __('dashboard.sar') }}</small>
                                    <div class="mt-1" style="color: #dc2626; font-size: 12px;">{{ __('dashboard.total_expense') }}</div>
                                </div>
                            </div>
                        </div>
                        <hr style="border-color: #e2e8f0;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">{{ __('dashboard.credit_balance') }}</span>
                            <span class="fw-bold" style="color: #10b981;">{{ number_format(($totalRevenue ?? 0) - ($totalExpense ?? 0)) }} {{ __('dashboard.sar') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('dashboard.debit_balance') }}</span>
                            <span class="fw-bold" style="color: #3b82f6;">{{ number_format(($totalRevenue ?? 0) - ($totalExpense ?? 0)) }} {{ __('dashboard.sar') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Occupancy Rate -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-header">
                        <h6><i class="fas fa-percentage me-2" style="color: #8b5cf6;"></i>{{ __('dashboard.occupancy_rate') }}</h6>
                    </div>
                    <div class="p-4 text-center">
                        <div class="mb-4">
                            <span class="big-number" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $occupancyRate ?? 0 }}%</span>
                            <div class="text-muted mt-2">{{ __('dashboard.overall_occupancy') }}</div>
                        </div>
                        <div class="progress-thin mb-4">
                            <div class="progress-bar bg-gradient" style="width: {{ $occupancyRate ?? 0 }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3" style="background: #f0fdf4; border-radius: 10px;">
                                    <div class="fw-bold" style="font-size: 24px; color: #10b981;">{{ $vacantUnits ?? 0 }}</div>
                                    <small class="text-muted">{{ __('dashboard.vacant') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3" style="background: #fef2f2; border-radius: 10px;">
                                    <div class="fw-bold" style="font-size: 24px; color: #ef4444;">{{ $occupiedUnits ?? 0 }}</div>
                                    <small class="text-muted">{{ __('dashboard.occupied') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row g-4 mb-4">

            <!-- Housekeeping Status -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h6><i class="fas fa-broom me-2" style="color: #f59e0b;"></i>{{ __('dashboard.housekeeping_status') }}</h6>
                        <a href="{{ route('dashboard.housekeeping_status.index') }}" class="btn btn-sm btn-primary" style="background: #6366f1; border: none;">{{ __('dashboard.view_all') }}</a>
                    </div>
                    <div class="p-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #10b981;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.vacant_clean') }}</span>
                                    <span class="fw-bold">{{ $hkVacantClean ?? 0 }}</span>
                                </div>
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #f59e0b;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.vacant_dirty') }}</span>
                                    <span class="fw-bold">{{ $hkVacantDirty ?? 0 }}</span>
                                </div>
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #3b82f6;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.occupied_clean') }}</span>
                                    <span class="fw-bold">{{ $hkOccupiedClean ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #ef4444;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.occupied_dirty') }}</span>
                                    <span class="fw-bold">{{ $hkOccupiedDirty ?? 0 }}</span>
                                </div>
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #8b5cf6;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.maintenance') }}</span>
                                    <span class="fw-bold">{{ $hkMaintenance ?? 0 }}</span>
                                </div>
                                <div class="hk-status">
                                    <span class="hk-dot" style="background: #6b7280;"></span>
                                    <span class="flex-grow-1 text-muted">{{ __('dashboard.blocked') }}</span>
                                    <span class="fw-bold">{{ $hkBlocked ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <h6><i class="fas fa-bolt me-2" style="color: #f59e0b;"></i>{{ __('dashboard.quick_actions') }}</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-4">
                                <a href="{{ route('dashboard.reservation.create') }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>{{ __('dashboard.new_booking') }}</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('dashboard.reservation.index', ['status' => 'arrival']) }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-user-check"></i>
                                    <span>{{ __('dashboard.check_in') }}</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('dashboard.reservation.index', ['status' => 'departure']) }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-user-times"></i>
                                    <span>{{ __('dashboard.check_out') }}</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('dashboard.housekeeping_status.index') }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-broom"></i>
                                    <span>{{ __('dashboard.housekeeping') }}</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('dashboard.invoice.index') }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>{{ __('dashboard.invoice') }}</span>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('setup-sidebar.setup_reservation.index') }}" class="quick-action-btn d-block text-decoration-none">
                                    <i class="fas fa-cog"></i>
                                    <span>{{ __('dashboard.settings') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bottom Stats -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="dashboard-card text-center">
                    <div class="stat-icon success mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="text-light small fw-medium mb-1">{{ __('dashboard.total_properties') }}</div>
                    <div class="fw-bold text-light" style="font-size: 32px; color: #1e293b;">{{ $totalProperties ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card text-center">
                    <div class="stat-icon primary mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="text-light small fw-medium mb-1">{{ __('dashboard.total_units') }}</div>
                    <div class="fw-bold text-light" style="font-size: 32px; color: #1e293b;">{{ $totalUnits ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card text-center">
                    <div class="stat-icon warning mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="text-light small fw-medium mb-1">{{ __('dashboard.total_guests') }}</div>
                    <div class="fw-bold text-light" style="font-size: 32px; color: #1e293b;">{{ number_format($totalGuests ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card text-center">
                    <div class="stat-icon info mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="text-light small fw-medium mb-1">{{ __('dashboard.avg_rating') }}</div>
                    <div class="fw-bold text-light" style="font-size: 32px; color: #1e293b;">4.8 <small class="text-light" style="font-size: 16px;">/5</small></div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("dashboard.reservation.notifications") }}', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json()).catch(() => {});
});
</script>
@endpush
