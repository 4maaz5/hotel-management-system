@extends('layouts.super_admin')

@section('title', 'Analytics')
@section('page_title', 'Platform Analytics')

@push('styles')
    <style>
        .analytics-kpi {
            border: 0;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
        }

        .analytics-kpi::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 4px;
            background: var(--accent);
        }

        .analytics-kpi__icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--accent) 12%, white);
            color: var(--accent);
        }

        .analytics-status-bar {
            display: flex;
            height: 14px;
            border-radius: 999px;
            overflow: hidden;
            background: #eef2f7;
        }

        .analytics-status-bar__segment {
            min-width: 2px;
        }

        .analytics-plan-bar {
            height: 10px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
        }

        .analytics-plan-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--accent);
        }

        .analytics-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent);
            display: inline-block;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Platform Analytics</h2>
                <p class="text-muted mb-0">Live subscription, usage, and growth metrics across all tenants.</p>
            </div>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-building me-1"></i>Review Tenants
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100 analytics-kpi" style="--accent:#2563eb;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Monthly Recurring Revenue</div>
                                <div class="fs-3 fw-bold">SAR {{ number_format($stats['monthlyRecurringRevenue'], 2) }}</div>
                                <div class="small text-muted">ARR SAR {{ number_format($stats['annualRecurringRevenue'], 2) }}</div>
                            </div>
                            <span class="analytics-kpi__icon"><i class="fas fa-chart-line"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 analytics-kpi" style="--accent:#16a34a;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Active Tenants</div>
                                <div class="fs-3 fw-bold">{{ $stats['activeTenants'] }}</div>
                                <div class="small text-muted">of {{ $stats['totalTenants'] }} total</div>
                            </div>
                            <span class="analytics-kpi__icon"><i class="fas fa-building"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 analytics-kpi" style="--accent:#0891b2;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Tenant Users</div>
                                <div class="fs-3 fw-bold">{{ $stats['totalUsers'] }}</div>
                                <div class="small text-muted">linked to tenants</div>
                            </div>
                            <span class="analytics-kpi__icon"><i class="fas fa-users"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 analytics-kpi" style="--accent:#d97706;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Properties</div>
                                <div class="fs-3 fw-bold">{{ $stats['totalProperties'] }}</div>
                                <div class="small text-muted">ARPT SAR {{ number_format($stats['averageRevenuePerTenant'], 2) }}</div>
                            </div>
                            <span class="analytics-kpi__icon"><i class="fas fa-hotel"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Tenant Status</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $statusPalette = [
                                'active' => '#16a34a',
                                'inactive' => '#64748b',
                                'suspended' => '#dc2626',
                            ];
                            $statusTotal = max(1, array_sum($tenantStatusCounts));
                        @endphp
                        <div class="analytics-status-bar mb-3" aria-hidden="true">
                            @foreach ($tenantStatusCounts as $status => $total)
                                @php
                                    $statusColor = $statusPalette[$status] ?? '#2563eb';
                                    $statusPercent = max(2, round(($total / $statusTotal) * 100));
                                @endphp
                                <span class="analytics-status-bar__segment" style="width: {{ $statusPercent }}%; background: {{ $statusColor }};"></span>
                            @endforeach
                        </div>
                        @forelse ($tenantStatusCounts as $status => $total)
                            @php
                                $statusColor = $statusPalette[$status] ?? '#2563eb';
                                $statusPercent = $stats['totalTenants'] > 0 ? round(($total / $stats['totalTenants']) * 100) : 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="--accent:{{ $statusColor }};">
                                <span class="text-capitalize"><span class="analytics-dot me-2"></span>{{ $status ?: 'Unknown' }}</span>
                                <span class="small fw-semibold">{{ $total }} · {{ $statusPercent }}%</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No tenant status data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Plan Distribution</h5>
                    </div>
                    <div class="card-body">
                        @forelse ($planDistribution as $plan)
                            @php
                                $colors = ['#2563eb', '#16a34a', '#d97706', '#7c3aed', '#0891b2'];
                                $color = $colors[$loop->index % count($colors)];
                                $percent = $stats['totalTenants'] > 0 ? min(100, round(($plan->tenants_count / $stats['totalTenants']) * 100)) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold">{{ $plan->name }}</span>
                                    <span>{{ $plan->tenants_count }} tenant(s) · {{ $percent }}%</span>
                                </div>
                                <div class="analytics-plan-bar" style="--accent: {{ $color }};">
                                    <span style="width: {{ $percent }}%;"></span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No plans created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Expiring Soon</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($expiringTenants as $tenant)
                            @php
                                $daysLeft = today()->diffInDays($tenant->end_date, false);
                                $urgency = $daysLeft <= 7 ? 'danger' : 'warning text-dark';
                            @endphp
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <span class="fw-semibold d-block">{{ $tenant->name }}</span>
                                    <small class="text-muted">{{ $tenant->plan?->name ?: 'No plan' }} · {{ $daysLeft }} day(s) left</small>
                                </span>
                                <span class="badge bg-{{ $urgency }}">{{ $tenant->end_date?->format('Y-m-d') }}</span>
                            </a>
                        @empty
                            <div class="list-group-item text-muted">No subscriptions expire in the next 30 days.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
