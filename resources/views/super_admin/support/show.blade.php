@extends('layouts.super_admin')

@section('title', 'Support Ticket')
@section('page_title', 'Support Ticket')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">{{ $ticket->subject }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-{{ $ticket->statusBadgeClass() }}">{{ ucfirst($ticket->status) }}</span>
                    <span class="badge bg-{{ $ticket->priorityBadgeClass() }}">{{ ucfirst($ticket->priority) }}</span>
                    <span class="badge bg-info">{{ $ticket->areaLabel() }}</span>
                    <span class="badge bg-light text-dark">{{ $ticket->tenant?->name ?: 'Unknown tenant' }}</span>
                    <span class="badge bg-light text-dark">{{ $ticket->category ?: 'General support' }}</span>
                </div>
            </div>
            <a href="{{ route('super-admin.support.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body support-thread">
                @foreach ($ticket->messages as $message)
                    @php
                        $isAdmin = $message->sender_role === 'super_admin';
                    @endphp
                    <div class="support-message {{ $isAdmin ? 'support-message--admin' : 'support-message--tenant' }}">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <div>
                                <div class="fw-semibold">{{ $message->user?->name ?: ($isAdmin ? 'SaaS Support' : 'Tenant User') }}</div>
                                <small class="text-muted">{{ $isAdmin ? 'SaaS Support' : ($ticket->tenant?->name ?: 'Tenant') }}</small>
                            </div>
                            <small class="text-muted">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                        </div>

                        @if ($message->body)
                            <div class="support-message__body">{!! $message->body !!}</div>
                        @endif

                        @if ($message->attachments->isNotEmpty())
                            <div class="support-attachment-grid">
                                @foreach ($message->attachments as $attachment)
                                    <a class="support-attachment" href="{{ route('super-admin.support.attachments.download', $attachment) }}">
                                        @if (\Illuminate\Support\Str::startsWith((string) $attachment->mime_type, 'image/'))
                                            <img src="{{ asset('storage/'.$attachment->path) }}" alt="{{ $attachment->original_name }}">
                                        @else
                                            <i class="fas fa-paperclip"></i>
                                        @endif
                                        <span>{{ \Illuminate\Support\Str::limit($attachment->original_name, 42) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('super-admin.support.reply', $ticket) }}" enctype="multipart/form-data" class="card shadow-sm">
                    @csrf
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Reply to Tenant</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @include('support.partials.rich-editor', ['editorId' => 'support-admin-reply-editor'])

                        <div class="row g-3 mt-1">
                            <div class="col-md-8">
                                <label class="form-label">Attachments</label>
                                <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                                <small class="text-muted">Up to 5 files, 5 MB each.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Set status</label>
                                <select name="status" class="form-select">
                                    @foreach (\App\Models\SupportTicket::STATUSES as $status)
                                        <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Send Reply
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <form method="POST" action="{{ route('super-admin.support.status', $ticket) }}" class="card shadow-sm">
                    @csrf
                    @method('PATCH')
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ticket Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="small text-muted">Dashboard</div>
                            <div class="fw-semibold">{{ $ticket->areaLabel() }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-muted">Tenant</div>
                            <div class="fw-semibold">{{ $ticket->tenant?->name ?: 'Unknown tenant' }}</div>
                            <div class="small text-muted">{{ $ticket->creator?->email }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach (\App\Models\SupportTicket::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($ticket->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="small text-muted">
                            Created {{ $ticket->created_at->format('Y-m-d H:i') }}<br>
                            Last updated {{ optional($ticket->last_message_at ?? $ticket->updated_at)->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <button type="submit" class="btn btn-outline-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
