@extends('layouts.app')

@section('title', 'Website Configuration')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Website Configuration</h2>
                    <p class="text-muted mb-0">Control the homepage, default SEO, contact overrides, and the bilingual messaging used across the public booking website.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('booking.home') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">
                        <i class="fas fa-up-right-from-square me-2"></i>Preview Website
                    </a>
                    <a href="{{ route('booking.rooms.index') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fas fa-bed me-2"></i>Preview Rooms
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Managed Pages</div>
                            <div class="fs-3 fw-bold">{{ $pages->count() }}</div>
                            <div class="text-muted">Static pages managed from Channel Manager.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Published Pages</div>
                            <div class="fs-3 fw-bold">{{ $pages->where('is_published', true)->count() }}</div>
                            <div class="text-muted">Pages that can appear on the public website.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">FAQ Items</div>
                            <div class="fs-3 fw-bold">{{ $faqCount }}</div>
                            <div class="text-muted">Questions available for the bilingual FAQ page.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Public Languages</div>
                            <div class="fs-3 fw-bold">EN / AR</div>
                            <div class="text-muted">The website content is managed in both English and Arabic.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <a href="{{ route('setup-sidebar.manage_product.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="btn btn-light rounded-circle"><i class="fas fa-box-open"></i></span>
                                <div class="fw-semibold">Manage Products</div>
                            </div>
                            <p class="text-muted mb-0">Publish room products, control website slugs, and keep room-level SEO aligned with inventory and unit content.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="{{ route('setup-sidebar.website_pages.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="btn btn-light rounded-circle"><i class="fas fa-file-lines"></i></span>
                                <div class="fw-semibold">Website Pages</div>
                            </div>
                            <p class="text-muted mb-0">Edit FAQ and Contact page copy plus page-by-page SEO metadata.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="{{ route('setup-sidebar.website_faq.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-dark">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="btn btn-light rounded-circle"><i class="fas fa-circle-question"></i></span>
                                <div class="fw-semibold">FAQ Content</div>
                            </div>
                            <p class="text-muted mb-0">Maintain the public FAQ page with bilingual answers that match how the operation actually works.</p>
                        </div>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('setup-sidebar.website_configuration.update') }}">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Homepage Hero</h5>
                        <p class="text-muted mb-0">These fields shape the first section users see when they land on the direct website.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Site Tagline EN</label>
                                <input type="text" name="site_tagline_en" class="form-control" value="{{ old('site_tagline_en', $settings->site_tagline_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Site Tagline AR</label>
                                <input type="text" name="site_tagline_ar" class="form-control" value="{{ old('site_tagline_ar', $settings->site_tagline_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Kicker EN</label>
                                <input type="text" name="home_hero_kicker_en" class="form-control" value="{{ old('home_hero_kicker_en', $settings->home_hero_kicker_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Kicker AR</label>
                                <input type="text" name="home_hero_kicker_ar" class="form-control" value="{{ old('home_hero_kicker_ar', $settings->home_hero_kicker_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Title EN</label>
                                <input type="text" name="home_hero_title_en" class="form-control" value="{{ old('home_hero_title_en', $settings->home_hero_title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Title AR</label>
                                <input type="text" name="home_hero_title_ar" class="form-control" value="{{ old('home_hero_title_ar', $settings->home_hero_title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Copy EN</label>
                                <textarea name="home_hero_text_en" class="form-control" rows="4">{{ old('home_hero_text_en', $settings->home_hero_text_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hero Copy AR</label>
                                <textarea name="home_hero_text_ar" class="form-control" rows="4">{{ old('home_hero_text_ar', $settings->home_hero_text_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Homepage Sections</h5>
                        <p class="text-muted mb-0">Use this copy to make the rooms and facilities sections feel more intentional and conversion-focused.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h6 class="fw-semibold mb-3">Featured Rooms</h6>
                                <label class="form-label">Title EN</label>
                                <input type="text" name="featured_rooms_title_en" class="form-control mb-3" value="{{ old('featured_rooms_title_en', $settings->featured_rooms_title_en) }}">
                                <label class="form-label">Title AR</label>
                                <input type="text" name="featured_rooms_title_ar" class="form-control mb-3" value="{{ old('featured_rooms_title_ar', $settings->featured_rooms_title_ar) }}">
                                <label class="form-label">Intro EN</label>
                                <textarea name="featured_rooms_intro_en" class="form-control mb-3" rows="4">{{ old('featured_rooms_intro_en', $settings->featured_rooms_intro_en) }}</textarea>
                                <label class="form-label">Intro AR</label>
                                <textarea name="featured_rooms_intro_ar" class="form-control" rows="4">{{ old('featured_rooms_intro_ar', $settings->featured_rooms_intro_ar) }}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="fw-semibold mb-3">Facilities Section</h6>
                                <label class="form-label">Title EN</label>
                                <input type="text" name="facilities_section_title_en" class="form-control mb-3" value="{{ old('facilities_section_title_en', $settings->facilities_section_title_en) }}">
                                <label class="form-label">Title AR</label>
                                <input type="text" name="facilities_section_title_ar" class="form-control mb-3" value="{{ old('facilities_section_title_ar', $settings->facilities_section_title_ar) }}">
                                <label class="form-label">Intro EN</label>
                                <textarea name="facilities_section_intro_en" class="form-control mb-3" rows="4">{{ old('facilities_section_intro_en', $settings->facilities_section_intro_en) }}</textarea>
                                <label class="form-label">Intro AR</label>
                                <textarea name="facilities_section_intro_ar" class="form-control" rows="4">{{ old('facilities_section_intro_ar', $settings->facilities_section_intro_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Rooms Page, Contact Overrides, and Footer</h5>
                        <p class="text-muted mb-0">This block shapes the room listing header and lets you override guest-facing contact details without changing the core property record.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Rooms Page Title EN</label>
                                <input type="text" name="rooms_page_title_en" class="form-control" value="{{ old('rooms_page_title_en', $settings->rooms_page_title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rooms Page Title AR</label>
                                <input type="text" name="rooms_page_title_ar" class="form-control" value="{{ old('rooms_page_title_ar', $settings->rooms_page_title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rooms Page Intro EN</label>
                                <textarea name="rooms_page_intro_en" class="form-control" rows="4">{{ old('rooms_page_intro_en', $settings->rooms_page_intro_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rooms Page Intro AR</label>
                                <textarea name="rooms_page_intro_ar" class="form-control" rows="4">{{ old('rooms_page_intro_ar', $settings->rooms_page_intro_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone Override</label>
                                <input type="text" name="contact_phone_override" class="form-control" value="{{ old('contact_phone_override', $settings->contact_phone_override) }}" placeholder="+966...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Email Override</label>
                                <input type="email" name="contact_email_override" class="form-control" value="{{ old('contact_email_override', $settings->contact_email_override) }}" placeholder="info@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Footer Note EN</label>
                                <textarea name="footer_note_en" class="form-control" rows="4">{{ old('footer_note_en', $settings->footer_note_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Footer Note AR</label>
                                <textarea name="footer_note_ar" class="form-control" rows="4">{{ old('footer_note_ar', $settings->footer_note_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-1">Default SEO</h5>
                        <p class="text-muted mb-0">These values are used for pages that do not have their own SEO metadata. Room pages are managed from Manage Products and static page metadata is managed from Website Pages.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Default SEO Title EN</label>
                                <input type="text" name="default_seo_title_en" class="form-control" value="{{ old('default_seo_title_en', $settings->default_seo_title_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default SEO Title AR</label>
                                <input type="text" name="default_seo_title_ar" class="form-control" value="{{ old('default_seo_title_ar', $settings->default_seo_title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default SEO Description EN</label>
                                <textarea name="default_seo_description_en" class="form-control" rows="4">{{ old('default_seo_description_en', $settings->default_seo_description_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default SEO Description AR</label>
                                <textarea name="default_seo_description_ar" class="form-control" rows="4">{{ old('default_seo_description_ar', $settings->default_seo_description_ar) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-floppy-disk me-2"></i>Save Website Settings
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
