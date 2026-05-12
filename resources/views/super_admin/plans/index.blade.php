@extends('layouts.super_admin')

@section('title', 'Subscription Plans')
@section('page_title', 'Subscription Plans')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Pricing Plans</h2>
                <p class="text-muted mb-0">Manage subscription tiers, feature sets, and usage limits.</p>
            </div>
            <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>New Plan
            </a>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse ($plans as $plan)
                @php
                    $isPopular = $plan->name === 'Pro' || (!$loop->first && !$loop->last && $plan->is_active);
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 16px;">
                        @if ($isPopular)
                            <div class="position-absolute top-0 start-50 translate-middle">
                                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                                    <i class="fas fa-star me-1"></i>Most Popular
                                </span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column p-4" style="padding-top: {{ $isPopular ? '2.5rem' : '1.5rem' }} !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                                    @if ($plan->description)
                                        <p class="text-muted small mb-0">{{ $plan->description }}</p>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }} text-uppercase" style="font-size: 0.65rem;">
                                    {{ $plan->is_active ? 'Live' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="text-center my-4">
                                <span class="display-5 fw-bold">${{ $plan->formattedPrice() }}</span>
                                <span class="text-muted" style="font-size: 0.9rem;">/ {{ $plan->billing_period }}</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-users text-muted" style="width: 18px;"></i>
                                    <small class="text-muted">Max Users:</small>
                                    <span class="fw-semibold ms-auto">{{ $plan->maxLimit('max_users') > 0 ? $plan->maxLimit('max_users') : 'Unlimited' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-building text-muted" style="width: 18px;"></i>
                                    <small class="text-muted">Max Properties:</small>
                                    <span class="fw-semibold ms-auto">{{ $plan->maxLimit('max_properties') > 0 ? $plan->maxLimit('max_properties') : 'Unlimited' }}</span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <small class="text-muted text-uppercase fw-semibold">Features</small>
                                @if ($plan->features)
                                    <div class="mt-2">
                                        @foreach ($plan->features as $feature)
                                            <div class="d-flex align-items-start gap-2 py-1">
                                                <i class="fas fa-check-circle text-success mt-1" style="font-size: 0.85rem;"></i>
                                                <span style="font-size: 0.9rem;">{{ $plan->featureList()[$feature] ?? $feature }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted small mt-2 mb-0">No features assigned.</p>
                                @endif
                            </div>

                            <div class="mt-auto d-flex gap-2 pt-3 border-top">
                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-outline-primary flex-fill rounded-pill">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('super-admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete the &quot;{{ $plan->name }}&quot; plan? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger rounded-pill">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body text-center text-muted py-5">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-4">
                                    <i class="fas fa-credit-card fa-3x opacity-25"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold">No Plans Yet</h5>
                            <p class="mb-3">Create your first subscription plan to get started.</p>
                            <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-plus me-1"></i>Create Plan
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $plans->links() }}
        </div>
    </div>
@endsection
