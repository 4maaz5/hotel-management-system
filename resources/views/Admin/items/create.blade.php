@extends('layouts.app')

@section('title', 'Create Outlet Item')

@section('content')

    <div class="container-fluid bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('dashboard.item_details') }}</h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form method="POST" action="{{ route('setup-sidebar.items.store') }}">
                    @csrf

                    <div class="row g-3">

                        {{-- Name --}}
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.name') }}
                            </label>

                            <input type="text" name="name" class="form-control"
                                placeholder="{{ __('dashboard.enter_name') }}">
                        </div>

                        {{-- Type --}}
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.type') }} *
                            </label>

                            <select name="type" class="form-select" required>
                                <option value="">
                                    {{ __('dashboard.select_type') }}
                                </option>

                                <option value="product">
                                    {{ __('dashboard.product') }}
                                </option>

                                <option value="service">
                                    {{ __('dashboard.service') }}
                                </option>
                            </select>
                        </div>

                        {{-- Outlet --}}
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.outlet') }} *
                            </label>

                            <select name="outlet_id" class="form-select" required id="outletSelect">

                                <option value="">
                                    {{ __('dashboard.select_outlet') }}
                                </option>

                                @foreach ($outlets ?? [] as $outlet)
                                    <option value="{{ $outlet->id }}">
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

                            <select name="category_id" id="categorySelect" class="form-select">

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

                            <textarea name="description" class="form-control" rows="4" placeholder="{{ __('dashboard.enter_description') }}"></textarea>
                        </div>

                        {{-- Price --}}
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.suggested_price') }} *
                            </label>

                            <input type="number" step="0.01" name="price" class="form-control">
                        </div>

                        {{-- Flags --}}
                        <div class="col-lg-6">

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="no_tax">

                                <label class="form-check-label">
                                    {{ __('dashboard.tax_exempted') }}
                                </label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="no_price">

                                <label class="form-check-label">
                                    {{ __('dashboard.free_item') }}
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="price_is_user_defined">

                                <label class="form-check-label">
                                    {{ __('dashboard.price_user_defined') }}
                                </label>
                            </div>

                        </div>

                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button type="submit" name="action" value="save_new" class="btn btn-primary">
                            {{ __('dashboard.save_add_new') }}
                        </button>

                        <button type="submit" name="action" value="save" class="btn btn-success">
                            {{ __('dashboard.save') }}
                        </button>

                        <a href="{{ route('setup-sidebar.items.index') }}" type="reset" class="btn btn-light border">
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

        document.getElementById('outletSelect').addEventListener('change', function() {

            let outletId = this.value;

            let categorySelect = document.getElementById('categorySelect');

            categorySelect.innerHTML = `<option value="">${loadingLabel}</option>`;

            if (!outletId) {
                categorySelect.innerHTML = `<option value="">${selectCategoryLabel}</option>`;
                return;
            }

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
                        options += `<option value="${cat.id}">${cat.name}</option>`;
                    });

                    categorySelect.innerHTML = options;

                })
                .catch(error => {
                    console.error('Failed to load categories:', error);
                    categorySelect.innerHTML = `<option value="">${selectCategoryLabel}</option>`;
                });

        });
    </script>
@endpush
