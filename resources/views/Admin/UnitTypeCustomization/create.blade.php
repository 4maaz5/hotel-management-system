@extends('layouts.app')
@section('title', 'Create Unit Type')

<style>
    /* Image Upload */
    .img-upload {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .img-upload:hover {
        border-color: var(--primary-color);
        background-color: rgba(26, 115, 232, 0.05);
    }

    .img-upload__icon {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .img-upload__label {
        color: #495057;
        font-weight: 500;
    }

    .img-upload__size-info {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
</style>
@section('content')
 <div class="bg-white p-3" style="border-radius:10px;">
    <div class="page-title">{{ __('dashboard.units') }}</div>
    <div class="page-subtitle">{{ __('dashboard.type_customization') }}</div>
    <form method="POST" action="{{ route('setup-sidebar.typeCustomization.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.unit_type') }}</label>
                <select name="unit_type_id" class="form-select">
                    <option value="">{{ __('dashboard.select_unit_type') }}</option>
                    @foreach ($unitTypes as $unitType)
                        <option value="{{ $unitType->id }}" {{ old('unit_type_id') == $unitType->id ? 'selected' : '' }}>
                            {{ $unitType->name }}
                        </option>
                    @endforeach
                </select>
                @error('unit_type_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.name') }}</label>
                <input type="text" name="name" class="form-control"
                    placeholder="e.g. {{ __('dashboard.deluxe_king') }}" value="{{ old('name') }}">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.unit_area') }} (m²)</label>
                <input type="number" step="0.01" name="unit_area" class="form-control" placeholder="45.50"
                    value="{{ old('unit_area') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.single_beds') }}</label>
                <input type="number" min="0" name="single_beds" class="form-control"
                    value="{{ old('single_beds', 0) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.double_beds') }}</label>
                <input type="number" min="0" name="double_beds" class="form-control"
                    value="{{ old('double_beds', 0) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('dashboard.base_accupancy') }}</label>
                <input type="number" min="1" name="base_occupancy" class="form-control"
                    value="{{ old('base_occupancy', 1) }}">
                @error('base_occupancy')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-12">
                <label class="form-label">{{ __('dashboard.description') }}</label>
                <textarea name="description" rows="3" class="form-control" placeholder="{{ __('dashboard.description') }}">{{ old('description') }}</textarea>
            </div>

            <div class="col-12">
                <hr>
                <h5 class="mb-3">Website Content and SEO</h5>
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Name (EN)</label>
                <input type="text" name="website_name_en" class="form-control" value="{{ old('website_name_en') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Name (AR)</label>
                <input type="text" name="website_name_ar" class="form-control" value="{{ old('website_name_ar') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Summary (EN)</label>
                <textarea name="website_summary_en" rows="2" class="form-control">{{ old('website_summary_en') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Summary (AR)</label>
                <textarea name="website_summary_ar" rows="2" class="form-control">{{ old('website_summary_ar') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Description (EN)</label>
                <textarea name="website_description_en" rows="4" class="form-control">{{ old('website_description_en') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Website Description (AR)</label>
                <textarea name="website_description_ar" rows="4" class="form-control">{{ old('website_description_ar') }}</textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Public Slug</label>
                <input type="text" name="website_slug" class="form-control" value="{{ old('website_slug') }}" placeholder="deluxe-king-room">
            </div>

            <div class="col-md-4">
                <label class="form-label">Display Order</label>
                <input type="number" min="0" name="website_sort_order" class="form-control" value="{{ old('website_sort_order', 0) }}">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="1" id="is_published_online" name="is_published_online" {{ old('is_published_online', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published_online">
                        Publish on website
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO Title (EN)</label>
                <input type="text" name="seo_title_en" class="form-control" value="{{ old('seo_title_en') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO Title (AR)</label>
                <input type="text" name="seo_title_ar" class="form-control" value="{{ old('seo_title_ar') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO Description (EN)</label>
                <textarea name="seo_description_en" rows="2" class="form-control">{{ old('seo_description_en') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">SEO Description (AR)</label>
                <textarea name="seo_description_ar" rows="2" class="form-control">{{ old('seo_description_ar') }}</textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('dashboard.images') }}</label>

                <div class="img-upload" id="imageUploadWrapper">
                    <div class="img-upload__icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="img-upload__label">
                        {{ __('dashboard.upload_images') }}
                    </div>
                    <div class="img-upload__size-info">
                        {{ __('dashboard.minimum_size') }} 400 × 300 px
                    </div>

                    <input type="file" accept="image/*" class="d-none" id="imagesUpload" name="images[]" multiple>
                </div>

                @error('images')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror

                @error('images.*')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror

                <div class="row g-2 mt-2" id="imagePreview"></div>
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.save') }}
                </button>
                <a href="{{ route('setup-sidebar.typeCustomization.index') }}" class="btn btn-secondary">
                    {{ __('dashboard.cancel') }}
                </a>
            </div>

        </div>
    </form>
 </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('imageUploadWrapper')
            .addEventListener('click', function() {
                document.getElementById('imagesUpload').click();
            });

        document.getElementById('imagesUpload')
            .addEventListener('change', function(event) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = '';

                Array.from(event.target.files).forEach(file => {
                    const reader = new FileReader();

                    reader.onload = e => {
                        const col = document.createElement('div');
                        col.className = 'col-4';

                        col.innerHTML = `
                        <div class="border rounded p-1 text-center">
                            <img src="${e.target.result}"
                                 class="img-fluid rounded"
                                 style="height:80px; object-fit:cover;">
                        </div>
                    `;
                        preview.appendChild(col);
                    };

                    reader.readAsDataURL(file);
                });
            });
    </script>
@endpush
