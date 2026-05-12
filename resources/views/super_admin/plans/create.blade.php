@extends('layouts.super_admin')

@section('title', 'Create Plan')
@section('page_title', 'Create Subscription Plan')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="mb-1">Create Plan</h2>
            <p class="text-muted mb-0">Define a new subscription tier with features and limits.</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.plans.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', '0') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Billing Period</label>
                            <select name="billing_period" class="form-select" required>
                                <option value="monthly" @selected(old('billing_period', 'monthly') === 'monthly')>Monthly</option>
                                <option value="yearly" @selected(old('billing_period') === 'yearly')>Yearly</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Limits</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Users</label>
                            <input type="number" min="0" name="max_users" value="{{ old('max_users', 0) }}" class="form-control">
                            <div class="form-text">0 = unlimited</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Properties</label>
                            <input type="number" min="0" name="max_properties" value="{{ old('max_properties', 0) }}" class="form-control">
                            <div class="form-text">0 = unlimited</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Features</h5>
                    <div class="row g-3">
                        @foreach ($plan->featureList() as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="features[]" value="{{ $key }}" class="form-check-input" id="feature_{{ $key }}"
                                        {{ in_array($key, old('features', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="feature_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active (visible for new sign-ups)</label>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
