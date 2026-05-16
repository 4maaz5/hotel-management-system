@extends('layouts.super_admin')

@section('title', 'Activity Log')
@section('page_title', 'Activity Log')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Activity Log</h2>
                <p class="text-muted mb-0">Audit trail of tenant and user actions across the platform.</p>
            </div>
            <a href="{{ route('super-admin.support.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-headset me-1"></i>Support Center
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Events</div>
                        <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Today</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['today'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Modules</div>
                        <div class="fs-3 fw-bold">{{ $stats['modules'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Users</div>
                        <div class="fs-3 fw-bold">{{ $stats['users'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.activity.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tenant</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">All Tenants</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected((string) $filters['tenant_id'] === (string) $tenant->id)>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select">
                            <option value="">All Modules</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected($filters['module'] === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="{{ route('super-admin.activity.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Audit Trail</h5>
                @unless ($hasActivityTable)
                    <span class="badge bg-warning text-dark">Migration Pending</span>
                @endunless
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Tenant</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (! $hasActivityTable)
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">The activity log table has not been migrated yet.</td>
                            </tr>
                        @else
                            @forelse ($activityLogs as $log)
                                <tr>
                                    <td class="text-nowrap">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->tenant?->name ?: '-' }}</td>
                                    <td>{{ $log->user?->name ?: '-' }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $log->module }}</span></td>
                                    <td>{{ $log->action }}</td>
                                    <td>
                                        <div>{{ $log->description }}</div>
                                        @if ($log->subject_reference)
                                            <small class="text-muted">{{ $log->subject_reference }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No activity records match the current filters.</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if ($hasActivityTable)
            <div class="mt-3">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>
@endsection
