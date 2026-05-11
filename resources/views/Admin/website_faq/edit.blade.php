@extends('layouts.app')

@section('title', 'Edit FAQ Item')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Edit FAQ Item</h2>
                    <p class="text-muted mb-0">Keep guest support answers accurate, short, and aligned with the live reservation workflow.</p>
                </div>
                <a href="{{ route('setup-sidebar.website_faq.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to FAQ
                </a>
            </div>

            <form method="POST" action="{{ route('setup-sidebar.website_faq.update', $item) }}">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Question EN</label>
                                <input type="text" name="question_en" class="form-control" value="{{ old('question_en', $item->question_en) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Question AR</label>
                                <input type="text" name="question_ar" class="form-control" value="{{ old('question_ar', $item->question_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Answer EN</label>
                                <textarea name="answer_en" class="form-control" rows="6" required>{{ old('answer_en', $item->answer_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Answer AR</label>
                                <textarea name="answer_ar" class="form-control" rows="6">{{ old('answer_ar', $item->answer_ar) }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order) }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published', $item->is_published))>
                                    <label class="form-check-label" for="is_published">Published</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-floppy-disk me-2"></i>Save FAQ Item
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
