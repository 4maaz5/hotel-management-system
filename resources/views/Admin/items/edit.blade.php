@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')

    <div class="container-fluid bg-white p-3" style="border-radius:15px;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('dashboard.edit_item') }}</h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form method="POST" action="{{ route('setup-sidebar.items.update', $item->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Status --}}
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ old('status', $item->status) ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold">
                                    {{ __('dashboard.status') }}
                                </label>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.name') }}
                            </label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}"
                                placeholder="{{ __('dashboard.enter_name') }}">
                        </div>

                        {{-- Type --}}
                        <div class="col-lg-3 col-md-4">

                            <label class="form-label fw-semibold">
                                {{ __('dashboard.type') }} *

                            </label>

                            <select name="type" class="form-select" required>

                                <option value="product" {{ old('type', $item->type) == 'product' ? 'selected' : '' }}>
                                    {{ __('dashboard.product') }}
                                </option>

                                <option value="service" {{ old('type', $item->type) == 'service' ? 'selected' : '' }}>
                                    {{ __('dashboard.service') }}
                                </option>

                            </select>
                        </div>

                        {{-- Outlet --}}
                        <div class="col-lg-3 col-md-4">

                            <label class="form-label fw-semibold">
                                {{ __('dashboard.outlet') }} *
                            </label>

                            <select name="outlet_id" id="outletSelect" class="form-select">

                                <option value="">
                                    {{ __('dashboard.select_outlet') }}
                                </option>

                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ old('outlet_id', $item->outlet_id) == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Category --}}
                        <div class="col-lg-3 col-md-4">

                            <label class="form-label fw-semibold">
                                {{ __('dashboard.category') }}
                            </label>

                            <select name="category_id" id="categorySelect" class="form-select"
                                data-selected="{{ old('category_id', $item->category_id) }}">

                                <option value="">
                                    {{ __('dashboard.select_category') }}
                                </option>

                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-lg-3 col-md-4">

                            <label class="form-label fw-semibold">
                                {{ __('dashboard.description') }}
                            </label>

                            <textarea name="description" class="form-control" rows="4" placeholder="{{ __('dashboard.enter_description') }}">{{ old('description', $item->description) }}</textarea>
                        </div>

                        {{-- Price --}}
                        <div class="col-lg-3 col-md-4">

                            <label class="form-label fw-semibold">
                                {{ __('dashboard.suggested_price') }} *
                            </label>

                            <input type="number" step="0.01" name="price" class="form-control"
                                value="{{ old('price', $item->price) }}">
                        </div>

                        {{-- Flags --}}
                        <div class="col-lg-6">

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="no_tax" value="1"
                                    {{ old('no_tax', $item->no_tax) ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.tax_exempted') }}
                                </label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="no_price" value="1"
                                    {{ old('no_price', $item->no_price) ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.free_item') }}
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="price_is_user_defined" value="1"
                                    {{ old('price_is_user_defined', $item->price_is_user_defined) ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.price_user_defined') }}
                                </label>
                            </div>

                        </div>

                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button type="submit" class="btn btn-success">
                            {{ __('dashboard.update') }}
                        </button>

                        <a href="{{ route('setup-sidebar.items.index') }}" class="btn btn-light border">
                            {{ __('dashboard.cancel') }}
                        </a>

                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        const outletCategoriesUrlTemplate = @json(route('setup-sidebar.items.categories', ['outlet' => '__OUTLET__']));
        const selectCategoryLabel = @json(__('dashboard.select_category'));
        const loadingLabel = 'Loading...';

        function loadCategories(outletId, selectedCategoryId = null) {

            let categorySelect = document.getElementById('categorySelect');

            if (!outletId) {
                categorySelect.innerHTML = `<option value="">${selectCategoryLabel}</option>`;
                return;
            }

            categorySelect.innerHTML = `<option value="">${loadingLabel}</option>`;

            const url = outletCategoriesUrlTemplate.replace('__OUTLET__', encodeURIComponent(outletId));

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to load categories (${response.status})`);
                    }

                    return response.json();
                })
                .then(data => {

                    let options = `<option value="">${selectCategoryLabel}</option>`;

                    data.forEach(cat => {

                        let selected = selectedCategoryId == cat.id ? 'selected' : '';

                        options += `<option value="${cat.id}" ${selected}>
                                ${cat.name}
                            </option>`;
                    });

                    categorySelect.innerHTML = options;

                })
                .catch(error => {
                    console.error('Failed to load categories:', error);
                    categorySelect.innerHTML = `<option value="">${selectCategoryLabel}</option>`;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {

            let outletSelect = document.getElementById('outletSelect');

            let categorySelect = document.getElementById('categorySelect');

            let selectedCategory = categorySelect.getAttribute('data-selected');

            let outletId = outletSelect.value;

            // Load categories on page load.
            loadCategories(outletId, selectedCategory);

            // Change event
            outletSelect.addEventListener('change', function() {
                loadCategories(this.value);
            });

        });
    </script>
@endpush
