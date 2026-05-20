@extends('layouts.app')

@section('title', __('support.tickets_title'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">{{ $ticket->subject }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-{{ $ticket->statusBadgeClass() }}">{{ ucfirst($ticket->status) }}</span>
                    <span class="badge bg-{{ $ticket->priorityBadgeClass() }}">{{ ucfirst($ticket->priority) }}</span>
                    <span class="badge bg-light text-dark">{{ $ticket->category ?: __('support.general_support') }}</span>
                </div>
            </div>
            <a href="{{ route('support.tickets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('support.back') }}
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
                                <div class="fw-semibold">{{ $message->user?->name ?: ($isAdmin ? __('support.saas_support') : __('support.saas_support')) }}</div>
                                <small class="text-muted">{{ $isAdmin ? __('support.saas_support') : __('support.your_team') }}</small>
                            </div>
                            <small class="text-muted">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                        </div>

                        @if ($message->body)
                            <div class="support-message__body">{!! $message->body !!}</div>
                        @endif

                        @if ($message->attachments->isNotEmpty())
                            <div class="support-attachment-grid">
                                @foreach ($message->attachments as $attachment)
                                    <a class="support-attachment" href="{{ route('support.tickets.attachments.download', $attachment) }}">
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

        <form method="POST" action="{{ route('support.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="card shadow-sm">
            @csrf
            <div class="card-header bg-white">
                <h5 class="mb-0">{{ __('support.reply_title') }}</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                @include('support.partials.rich-editor', ['editorId' => 'support-reply-editor'])

                <div class="mt-3">
                    <label class="form-label">{{ __('support.attachments_label') }}</label>
                    <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                    <small class="text-muted">{{ __('support.attachments_limit') }}</small>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>{{ __('support.send_reply') }}
                </button>
            </div>
        </form>
    </div>
@endsection
