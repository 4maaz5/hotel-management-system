@extends('layouts.super_admin')

@section('title', 'Tenants')
@section('page_title', 'Tenant Management')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Tenants</h2>
                <p class="text-muted mb-0">Create, review, and control tenant subscriptions.</p>
            </div>
            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">New Tenant</a>
        </div>

        {{-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif --}}

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th>Properties</th>
                            <th>Dates</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tenants as $tenant)
                            <tr>
                                <td>{{ $tenant->name }}</td>
                                <td>{{ $tenant->email ?: '-' }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $tenant->status }}</span></td>
                                <td>{{ $tenant->users_count }}</td>
                                <td>{{ $tenant->properties_count }}</td>
                                <td>{{ $tenant->start_date?->format('Y-m-d') }} / {{ $tenant->end_date?->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No tenants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $tenants->links() }}
        </div>
    </div>
@endsection
