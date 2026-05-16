@extends('layout.master')

@section('title', 'HR Support Tickets')

@push('styles')
    <style>
        .support-filter-bar {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
        }

        .support-filter-bar__controls {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 10px;
        }

        .support-filter-field {
            min-width: 210px;
        }

        .support-filter-field label {
            display: block;
            margin-bottom: 5px;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .support-filter-field .form-control {
            height: 38px;
            border-color: #d8dee9;
            border-radius: 8px;
            color: #1f2937;
            background-color: #fff;
            box-shadow: none;
        }

        .support-filter-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }

        .support-filter-actions .btn {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 8px;
            font-weight: 600;
            padding-left: 14px;
            padding-right: 14px;
        }

        .support-filter-actions .btn-link {
            color: #64748b;
            text-decoration: none;
        }

        .support-filter-actions .btn-link:hover {
            color: #1f2937;
            background: #f1f5f9;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title mb-1">HR Support Tickets</h2>
                    </div>
                    <a href="{{ route('dashboard.support.tickets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Ticket
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <form method="GET" class="support-filter-bar">
                            <div class="support-filter-bar__controls">
                                <div class="support-filter-field">
                                    <label for="hr-support-status-filter">Status</label>
                                    <select id="hr-support-status-filter" name="status" class="form-control">
                                        <option value="">All statuses</option>
                                        @foreach (\App\Models\SupportTicket::STATUSES as $status)
                                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="support-filter-actions">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="fas fa-filter"></i>Filter
                                    </button>
                                    @if (request()->has('status'))
                                        <a href="{{ route('dashboard.support.tickets.index') }}" class="btn btn-link">
                                            <i class="fas fa-rotate-left"></i>Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Messages</th>
                                    <th>Last Update</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $ticket->subject }}</div>
                                            <small class="text-muted">{{ $ticket->category ?: 'General support' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ str_replace(' text-dark', '', $ticket->priorityBadgeClass()) }}">{{ ucfirst($ticket->priority) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ str_replace(' text-dark', '', $ticket->statusBadgeClass()) }}">{{ ucfirst($ticket->status) }}</span>
                                        </td>
                                        <td>{{ $ticket->messages_count }}</td>
                                        <td>{{ optional($ticket->last_message_at ?? $ticket->updated_at)->diffForHumans() }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('dashboard.support.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">No support tickets yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($tickets->hasPages())
                        <div class="card-footer">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
