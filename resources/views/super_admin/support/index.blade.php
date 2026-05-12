@extends('layouts.super_admin')

@section('title', 'Support')
@section('page_title', 'Support Center')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Support Center</h2>
                <p class="text-muted mb-0">Manage tenant support tickets and knowledge base.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" disabled><i class="fas fa-plus me-1"></i>New Ticket</button>
                <button class="btn btn-outline-secondary" disabled><i class="fas fa-book me-1"></i>Knowledge Base</button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Open Tickets</div>
                        <div class="fs-3 fw-bold">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Pending</div>
                        <div class="fs-3 fw-bold text-warning">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Resolved</div>
                        <div class="fs-3 fw-bold text-success">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Avg Response</div>
                        <div class="fs-3 fw-bold">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tickets</h5>
                <span class="badge bg-secondary">Coming Soon</span>
            </div>
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-ticket-alt fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">Support ticket system will be available in a future update.</p>
            </div>
        </div>
    </div>
@endsection
