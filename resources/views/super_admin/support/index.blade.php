@extends('layouts.super_admin')

@section('title', 'Support')
@section('page_title', 'Support Center')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Support Tickets</h2>
                <p class="text-muted mb-0">Review tenant tickets, respond in-thread, and monitor account health.</p>
            </div>
            <a href="{{ route('super-admin.activity.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-history me-1"></i>View Activity
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">All Tickets</div>
                        <div class="fs-3 fw-bold">{{ $ticketStats['total'] }}</div>
                        <div class="small text-muted">Reservation {{ $ticketStats['reservation'] }} | HRM {{ $ticketStats['hr'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Open</div>
                        <div class="fs-3 fw-bold text-success">{{ $ticketStats['open'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Pending Tenant</div>
                        <div class="fs-3 fw-bold text-warning">{{ $ticketStats['pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Closed</div>
                        <div class="fs-3 fw-bold text-secondary">{{ $ticketStats['closed'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All statuses</option>
                            @foreach (\App\Models\SupportTicket::STATUSES as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-muted mb-1">Dashboard</label>
                        <select name="support_area" class="form-select form-select-sm">
                            <option value="">All dashboards</option>
                            @foreach (\App\Models\SupportTicket::AREAS as $area)
                                <option value="{{ $area }}" @selected(request('support_area') === $area)>
                                    {{ $area === 'hr' ? 'HRM' : 'Reservation' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-muted mb-1">Tenant</label>
                        <select name="tenant_id" class="form-select form-select-sm">
                            <option value="">All tenants</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected((string) request('tenant_id') === (string) $tenant->id)>{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" type="submit">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        @if (request()->hasAny(['status', 'support_area', 'tenant_id']))
                            <a href="{{ route('super-admin.support.index') }}" class="btn btn-sm btn-link text-decoration-none">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Dashboard</th>
                            <th>Tenant</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Messages</th>
                            <th>Last Update</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->subject }}</div>
                                    <small class="text-muted">{{ $ticket->category ?: 'General support' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $ticket->areaLabel() }}</span>
                                </td>
                                <td>
                                    <div>{{ $ticket->tenant?->name ?: 'Unknown tenant' }}</div>
                                    <small class="text-muted">{{ $ticket->creator?->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->priorityBadgeClass() }}">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->statusBadgeClass() }}">{{ ucfirst($ticket->status) }}</span>
                                </td>
                                <td>{{ $ticket->messages_count }}</td>
                                <td>{{ optional($ticket->last_message_at ?? $ticket->updated_at)->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('super-admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">No support tickets match the current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tickets->hasPages())
                <div class="card-footer bg-white">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">System Checks</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($checks as $check)
                            @php
                                $badge = match ($check['status']) {
                                    'healthy' => 'success',
                                    'warning' => 'warning text-dark',
                                    default => 'danger',
                                };
                            @endphp
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $check['label'] }}</div>
                                        <div class="small text-muted">{{ $check['detail'] }}</div>
                                    </div>
                                    <span class="badge bg-{{ $badge }} text-uppercase">{{ $check['status'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tenants Needing Attention</h5>
                        <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-outline-primary">All Tenants</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Status</th>
                                    <th>Usage</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attentionTenants as $tenant)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $tenant->name }}</div>
                                            <small class="text-muted">{{ $tenant->plan?->name ?: 'No plan' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-uppercase">{{ $tenant->subscription_status }}</span>
                                            @if ($tenant->end_date)
                                                <div class="small text-muted">Ends {{ $tenant->end_date->format('Y-m-d') }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="d-block">Users: {{ $tenant->users_count }} / {{ $tenant->maxLimit('max_users') ?: 'Unlimited' }}</small>
                                            <small class="d-block">Properties: {{ $tenant->properties_count }} / {{ $tenant->maxLimit('max_properties') ?: 'Unlimited' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No tenant support items need attention.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
