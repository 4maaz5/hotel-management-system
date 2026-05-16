@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Create Support Ticket</h2>
                <p class="text-muted mb-0">Share the issue details, screenshots, or files with the SaaS support team.</p>
            </div>
            <a href="{{ route('support.tickets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>

        <form method="POST" action="{{ route('support.tickets.store') }}" enctype="multipart/form-data" class="card shadow-sm">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-lg-8">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required maxlength="180">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category') }}" maxlength="80" placeholder="Billing, HR, Reservation">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select" required>
                            @foreach (\App\Models\SupportTicket::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message</label>
                        @include('support.partials.rich-editor', ['editorId' => 'support-create-editor'])
                    </div>
                    <div class="col-12">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                        <small class="text-muted">Up to 5 files, 5 MB each.</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Submit Ticket
                </button>
            </div>
        </form>
    </div>
@endsection
