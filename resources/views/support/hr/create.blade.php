@extends('layout.master')

@section('title', __('support.hr_create_title'))

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ __('support.hr_create_title') }}</h2>
                    </div>
                    <a href="{{ route('dashboard.support.tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>{{ __('support.back') }}
                    </a>
                </div>

                <form method="POST" action="{{ route('dashboard.support.tickets.store') }}" enctype="multipart/form-data" class="card">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-group col-lg-8">
                                <label>{{ __('support.subject_label') }}</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required maxlength="180">
                            </div>
                            <div class="form-group col-md-6 col-lg-2">
                                <label>{{ __('support.category_label') }}</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category', 'HR') }}" maxlength="80" placeholder="{{ __('support.hr_category_placeholder') }}">
                            </div>
                            <div class="form-group col-md-6 col-lg-2">
                                <label>{{ __('support.priority_label') }}</label>
                                <select name="priority" class="form-control" required>
                                    @foreach (\App\Models\SupportTicket::PRIORITIES as $priority)
                                        <option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('support.message_label') }}</label>
                            @include('support.partials.rich-editor', ['editorId' => 'hr-support-create-editor'])
                        </div>

                        <div class="form-group mb-0">
                            <label>{{ __('support.attachments_label') }}</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv">
                            <small class="text-muted">{{ __('support.attachments_limit') }}</small>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>{{ __('support.submit_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
