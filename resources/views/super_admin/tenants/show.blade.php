@extends('layouts.super_admin')

@section('title', 'Tenant Details')
@section('page_title', 'Tenant Details')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">{{ $tenant->name }}</h2>
                <p class="text-muted mb-0">Tenant subscription overview and owner account.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">All Tenants</a>
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-primary">Edit Tenant</a>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Subscription</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Status</div>
                                <div class="fw-semibold text-uppercase">{{ $tenant->status }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ $tenant->email ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Phone</div>
                                <div class="fw-semibold">{{ $tenant->phone ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Subscription Window</div>
                                <div class="fw-semibold">{{ $tenant->start_date?->format('Y-m-d') }} to {{ $tenant->end_date?->format('Y-m-d') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Users</div>
                                <div class="fw-semibold">{{ $tenant->users_count }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Properties</div>
                                <div class="fw-semibold">{{ $tenant->properties_count }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Owner User</h5>
                    </div>
                    <div class="card-body">
                        @if ($owner)
                            <div class="mb-2"><span class="text-muted small d-block">Name</span><strong>{{ $owner->name }}</strong></div>
                            <div class="mb-2"><span class="text-muted small d-block">Email</span><strong>{{ $owner->email }}</strong></div>
                            <div><span class="text-muted small d-block">User Type</span><strong>{{ $owner->user_type ?: 'owner' }}</strong></div>
                        @else
                            <p class="text-muted mb-0">No owner user is assigned yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
