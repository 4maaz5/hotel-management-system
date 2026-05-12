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
                                <div class="text-muted small">Plan</div>
                                <div class="fw-semibold">{{ $tenant->plan?->name ?: '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Subdomain</div>
                                <div class="fw-semibold"><code>{{ $tenant->subdomain ?: '-' }}</code></div>
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

                        @if ($tenant->plan)
                            <hr>
                            <h6 class="mb-2">Plan Features</h6>
                            <div class="row g-1">
                                @forelse ($tenant->plan->features ?? [] as $feature)
                                    <div class="col-md-6">
                                        <small><i class="fas fa-check-circle text-success me-1"></i>{{ $tenant->plan->featureList()[$feature] ?? $feature }}</small>
                                    </div>
                                @empty
                                    <div class="col-12"><small class="text-muted">No features assigned.</small></div>
                                @endforelse
                            </div>
                        @endif
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

                @if ($tenant->plan)
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Plan Limits</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="text-muted small d-block">Max Users</span>
                                <strong>{{ $tenant->plan->maxLimit('max_users') > 0 ? $tenant->plan->maxLimit('max_users') : 'Unlimited' }}</strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Max Properties</span>
                                <strong>{{ $tenant->plan->maxLimit('max_properties') > 0 ? $tenant->plan->maxLimit('max_properties') : 'Unlimited' }}</strong>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Price</span>
                                <strong>${{ $tenant->plan->formattedPrice() }} / {{ $tenant->plan->billing_period }}</strong>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
