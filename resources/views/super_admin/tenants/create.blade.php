@extends('layouts.super_admin')

@section('title', 'Create Tenant')
@section('page_title', 'Create Tenant')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="mb-1">Create Tenant</h2>
            <p class="text-muted mb-0">Create the tenant subscription and its owner user together.</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.tenants.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tenant Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" name="subdomain" value="{{ old('subdomain') }}" class="form-control" placeholder="grand-hyatt" required>
                                <span class="input-group-text">.yourplatform.com</span>
                            </div>
                            <div class="form-text">Only letters, numbers, and hyphens.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenant Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Subscription Plan</label>
                            <select name="subscription_plan_id" class="form-select">
                                <option value="">— Select Plan —</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('subscription_plan_id') == $plan->id)>
                                        {{ $plan->name }} (SAR {{ $plan->formattedPrice() }}/{{ $plan->billing_period }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', now()->addYear()->toDateString()) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (['active', 'inactive', 'suspended'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', 'active') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Owner User</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Owner Name</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Owner Email</label>
                            <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="owner_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="owner_password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-4 mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Tenant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
