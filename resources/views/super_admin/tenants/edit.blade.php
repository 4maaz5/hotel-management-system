@extends('layouts.super_admin')

@section('title', 'Edit Tenant')
@section('page_title', 'Edit Tenant')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="mb-1">Edit Tenant</h2>
            <p class="text-muted mb-0">Update subscription dates, status, and tenant owner access.</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.tenants.update', $tenant) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tenant Name</label>
                            <input type="text" name="name" value="{{ old('name', $tenant->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenant Email</label>
                            <input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $tenant->start_date?->format('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $tenant->end_date?->format('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (['active', 'inactive', 'suspended'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $tenant->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Owner User</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Owner Name</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $owner?->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Owner Email</label>
                            <input type="email" name="owner_email" value="{{ old('owner_email', $owner?->email) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="owner_password" class="form-control">
                            <div class="form-text">Leave blank to keep the current password.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="owner_password_confirmation" class="form-control">
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
                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
