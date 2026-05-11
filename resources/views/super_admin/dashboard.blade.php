@extends('layouts.super_admin')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Super Admin Dashboard')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Super Admin Dashboard</h2>
                <p class="text-muted mb-0">Manage SaaS tenants, subscriptions, and tenant owners from one place.</p>
            </div>
            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">Create Tenant</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Tenants</div>
                        <div class="fs-3 fw-bold">{{ $stats['totalTenants'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Active</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['activeTenants'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Expired</div>
                        <div class="fs-3 fw-bold text-warning">{{ $stats['expiredTenants'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Suspended</div>
                        <div class="fs-3 fw-bold text-danger">{{ $stats['suspendedTenants'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Tenant Users</div>
                        <div class="fs-3 fw-bold">{{ $stats['tenantUsers'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Properties</div>
                        <div class="fs-3 fw-bold">{{ $stats['properties'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tenants</h5>
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Subscription</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTenants as $tenant)
                            <tr>
                                <td>{{ $tenant->name }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $tenant->status }}</span></td>
                                <td>{{ $tenant->start_date?->format('Y-m-d') }} to {{ $tenant->end_date?->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No tenants created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
