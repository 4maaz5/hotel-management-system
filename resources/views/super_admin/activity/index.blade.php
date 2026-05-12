@extends('layouts.super_admin')

@section('title', 'Activity Log')
@section('page_title', 'Activity Log')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Activity Log</h2>
                <p class="text-muted mb-0">Audit trail of actions performed across all tenants.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" disabled><i class="fas fa-filter me-1"></i>Filter</button>
                <button class="btn btn-outline-primary" disabled><i class="fas fa-download me-1"></i>Export</button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Audit Trail</h5>
                <div>
                    <select class="form-select form-select-sm" disabled>
                        <option>All Tenants</option>
                    </select>
                </div>
            </div>
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">No recent activity recorded.</p>
                <p class="small mb-0">Activity logging will capture tenant creation, subscription changes, and user actions.</p>
            </div>
        </div>
    </div>
@endsection
