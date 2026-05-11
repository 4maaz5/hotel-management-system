@extends('layouts.app')

@section('title', 'Website Pages')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Website Pages</h2>
                    <p class="text-muted mb-0">Manage the public FAQ and Contact pages with bilingual content and SEO settings.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('setup-sidebar.website_configuration.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Website Configuration
                    </a>
                    <a href="{{ route('booking.home') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fas fa-up-right-from-square me-2"></i>Preview Website
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Page</th>
                                    <th class="py-3">Navigation</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">SEO</th>
                                    <th class="text-end px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages as $page)
                                    @php
                                        $previewRoute = match ($page->page_key) {
                                            'faq' => route('booking.faq'),
                                            'contact' => route('booking.contact'),
                                            default => route('booking.home'),
                                        };
                                        $seoReady = filled($page->seo_title_en) && filled($page->seo_description_en);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold">{{ $page->title_en ?: ucfirst($page->page_key) }}</div>
                                            <div class="text-muted small">{{ $page->title_ar ?: '-' }}</div>
                                        </td>
                                        <td class="py-3">
                                            @if ($page->show_in_navigation)
                                                <span class="badge bg-info-subtle text-info">Shown in nav</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden from nav</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($page->is_published)
                                                <span class="badge bg-success-subtle text-success">Published</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($seoReady)
                                                <span class="badge bg-success-subtle text-success">SEO ready</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Needs SEO</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-4 py-3">
                                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                                <a href="{{ $previewRoute }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                    Preview
                                                </a>
                                                <a href="{{ route('setup-sidebar.website_pages.edit', $page) }}" class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
