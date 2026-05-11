@extends('layouts.app')

@section('title', 'Website FAQ')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Website FAQ</h2>
                    <p class="text-muted mb-0">Publish bilingual questions and answers that reduce guest hesitation and keep the booking website aligned with operations.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('setup-sidebar.website_configuration.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Website Configuration
                    </a>
                    <a href="{{ route('booking.faq') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fas fa-up-right-from-square me-2"></i>Preview FAQ Page
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-1">Add FAQ Item</h5>
                    <p class="text-muted mb-0">Create guest-facing answers for payments, check-in, support, child policy, or other common questions.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('setup-sidebar.website_faq.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Question EN</label>
                                <input type="text" name="question_en" class="form-control" value="{{ old('question_en') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Question AR</label>
                                <input type="text" name="question_ar" class="form-control" value="{{ old('question_ar') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Answer EN</label>
                                <textarea name="answer_en" class="form-control" rows="4" required>{{ old('answer_en') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Answer AR</label>
                                <textarea name="answer_ar" class="form-control" rows="4">{{ old('answer_ar') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" checked>
                                    <label class="form-check-label" for="is_published">Published</label>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-plus me-2"></i>Add FAQ Item
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Question</th>
                                    <th class="py-3">Order</th>
                                    <th class="py-3">Status</th>
                                    <th class="text-end px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold">{{ $item->question_en }}</div>
                                            <div class="text-muted small">{{ $item->question_ar ?: '-' }}</div>
                                        </td>
                                        <td class="py-3">{{ $item->sort_order }}</td>
                                        <td class="py-3">
                                            @if ($item->is_published)
                                                <span class="badge bg-success-subtle text-success">Published</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-4 py-3">
                                            <div class="d-inline-flex gap-2">
                                                <a href="{{ route('setup-sidebar.website_faq.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" action="{{ route('setup-sidebar.website_faq.destroy', $item) }}" onsubmit="return confirm('Delete this FAQ item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-muted">No FAQ items yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
