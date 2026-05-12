@extends('layouts.super_admin')

@section('title', 'Analytics')
@section('page_title', 'Platform Analytics')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Platform Analytics</h2>
                <p class="text-muted mb-0">Usage statistics, revenue trends, and growth metrics across all tenants.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" disabled><i class="fas fa-file-export me-1"></i>Export Report</button>
                <div class="dropdown">
                    <select class="form-select form-select-sm" disabled>
                        <option>Last 30 days</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="text-muted small">Monthly Recurring Revenue</div>
                        <div class="fs-3 fw-bold">$0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body">
                        <div class="text-muted small">Active Tenants</div>
                        <div class="fs-3 fw-bold">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body">
                        <div class="text-muted small">Total Users</div>
                        <div class="fs-3 fw-bold">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body">
                        <div class="text-muted small">Total Properties</div>
                        <div class="fs-3 fw-bold">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Revenue Trend</h5>
                    </div>
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">Revenue chart will appear here once data is available.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Plan Distribution</h5>
                    </div>
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-chart-pie fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">Plan breakdown chart coming soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
