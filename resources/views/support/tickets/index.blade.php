@extends('layouts.app')

@section('title', __('support.tickets_title'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">{{ __('support.tickets_title') }}</h2>
                <p class="text-muted mb-0">{{ __('support.tickets_description') }}</p>
            </div>
            <a href="{{ route('support.tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>{{ __('support.create') }}
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="status" class="form-select form-select-sm" style="max-width: 180px;">
                        <option value="">{{ __('support.all_statuses') }}</option>
                        @foreach (\App\Models\SupportTicket::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-outline-primary" type="submit">
                        <i class="fas fa-filter me-1"></i>{{ __('support.filter') }}
                    </button>
                    @if (request()->has('status'))
                        <a href="{{ route('support.tickets.index') }}" class="btn btn-sm btn-link text-decoration-none">{{ __('support.reset') }}</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('support.th_ticket') }}</th>
                            <th>{{ __('support.th_priority') }}</th>
                            <th>{{ __('support.th_status') }}</th>
                            <th>{{ __('support.th_messages') }}</th>
                            <th>{{ __('support.th_last_update') }}</th>
                            <th class="text-end">{{ __('support.th_action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->subject }}</div>
                                    <small class="text-muted">{{ $ticket->category ?: __('support.general_support') }}</small>
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
                                    <a href="{{ route('support.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('support.open') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">{{ __('support.no_tickets') }}</td>
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
    </div>
@endsection
