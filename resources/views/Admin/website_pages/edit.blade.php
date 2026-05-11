@extends('layouts.app')

@section('title', 'Edit Website Page')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Edit {{ $page->title_en ?: ucfirst($page->page_key) }}</h2>
                    <p class="text-muted mb-0">Maintain bilingual page content, navigation labels, and search metadata for the public website.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('setup-sidebar.website_pages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Pages
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('setup-sidebar.website_pages.update', $page) }}">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Navigation and Page Title</h5>
                        <p class="text-muted mb-0">Keep labels short for navigation and use stronger titles for the page body and SEO.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Navigation Label EN</label>
                                <input type="text" name="nav_label_en" class="form-control" value="{{ old('nav_label_en', $page->nav_label_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Navigation Label AR</label>
                                <input type="text" name="nav_label_ar" class="form-control" value="{{ old('nav_label_ar', $page->nav_label_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Page Title EN</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $page->title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Page Title AR</label>
                                <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $page->title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Title EN</label>
                                <input type="text" name="hero_title_en" class="form-control" value="{{ old('hero_title_en', $page->hero_title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Title AR</label>
                                <input type="text" name="hero_title_ar" class="form-control" value="{{ old('hero_title_ar', $page->hero_title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Intro EN</label>
                                <textarea name="hero_intro_en" class="form-control" rows="4">{{ old('hero_intro_en', $page->hero_intro_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Intro AR</label>
                                <textarea name="hero_intro_ar" class="form-control" rows="4">{{ old('hero_intro_ar', $page->hero_intro_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Body Content</h5>
                        <p class="text-muted mb-0">Write long-form page content here. The public site will render line breaks automatically.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Body EN</label>
                                <textarea name="body_en" class="form-control" rows="12">{{ old('body_en', $page->body_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Body AR</label>
                                <textarea name="body_ar" class="form-control" rows="12">{{ old('body_ar', $page->body_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">SEO</h5>
                        <p class="text-muted mb-0">Each published page should have its own titles and descriptions for stronger organic visibility.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">SEO Title EN</label>
                                <input type="text" name="seo_title_en" class="form-control" value="{{ old('seo_title_en', $page->seo_title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SEO Title AR</label>
                                <input type="text" name="seo_title_ar" class="form-control" value="{{ old('seo_title_ar', $page->seo_title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SEO Description EN</label>
                                <textarea name="seo_description_en" class="form-control" rows="4">{{ old('seo_description_en', $page->seo_description_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SEO Description AR</label>
                                <textarea name="seo_description_ar" class="form-control" rows="4">{{ old('seo_description_ar', $page->seo_description_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SEO Keywords EN</label>
                                <input type="text" name="seo_keywords_en" class="form-control" value="{{ old('seo_keywords_en', $page->seo_keywords_en) }}" placeholder="hotel, direct booking, rooms">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SEO Keywords AR</label>
                                <input type="text" name="seo_keywords_ar" class="form-control" value="{{ old('seo_keywords_ar', $page->seo_keywords_ar) }}" placeholder="فندق، حجز مباشر، غرف">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Display Order</label>
                                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $page->sort_order) }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published', $page->is_published))>
                                    <label class="form-check-label" for="is_published">Published on website</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_in_navigation" id="show_in_navigation" value="1" @checked(old('show_in_navigation', $page->show_in_navigation))>
                                    <label class="form-check-label" for="show_in_navigation">Show in navigation</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-floppy-disk me-2"></i>Save Page
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
