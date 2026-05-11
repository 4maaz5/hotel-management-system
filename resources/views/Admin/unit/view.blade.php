@extends('layouts.app')

@section('title', 'Edit Unit')

<style>
    .parent-Contact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .contact-number.style-number {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .contact-number.background-icon,
    .contact-number.u-cursor-pointer {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    /* Page Header */
    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;"">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.units') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_unit') }}</div>
            </div>

        </div>
        <form method="POST" action="#">
            @csrf
            @method('PUT')
            <div class="row g-3">

                {{-- Unit Number --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_number') }}</label>
                    <input type="text" name="unit_number" class="form-control"
                        value="{{ old('unit_number', $unit->unit_number) }}" disabled>
                </div>

                {{-- Unit Class --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_class') }}</label>
                    <select name="unit_class_id" class="form-select" disabled>
                        <option value="">{{ __('dashboard.select') }}</option>
                        @foreach ($unitClasses as $class)
                            <option value="{{ $class->id }}"
                                {{ old('unit_class_id', $unit->unit_class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Unit Type --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_type') }}</label>
                    <select name="unit_type_id" class="form-select" disabled>
                        <option value="">{{ __('dashboard.select') }}</option>
                        @foreach ($unitTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('unit_type_id', $unit->unit_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Block --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.block') }}</label>
                    <select name="block_id" class="form-select" disabled>
                        @foreach ($blocks as $block)
                            <option value="{{ $block->id }}"
                                {{ old('block_id', $unit->block_id) == $block->id ? 'selected' : '' }}>
                                {{ $block->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Floor --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.floor') }}</label>
                    <select name="floor_id" class="form-select" disabled>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}"
                                {{ old('floor_id', $unit->floor_id) == $floor->id ? 'selected' : '' }}>
                                {{ $floor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hall Type --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.hall_type') }}</label>
                    <select name="hall_type_id" class="form-select" disabled>
                        <option value="">{{ __('dashboard.none') }}</option>
                        @foreach ($hallTypes as $hall)
                            <option value="{{ $hall->id }}"
                                {{ old('hall_type_id', $unit->hall_type_id) == $hall->id ? 'selected' : '' }}>
                                {{ $hall->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Phone Extension --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.phone_extension') }}</label>
                    <input type="text" name="phone_extension" class="form-control"
                        value="{{ old('phone_extension', $unit->phone_extension) }}" disabled>
                </div>

                {{-- Kitchen Type --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.kitchen_type') }}</label>
                    <input type="text" name="kitchen_type" class="form-control"
                        value="{{ old('kitchen_type', $unit->kitchen_type) }}" disabled>
                </div>

                {{-- Toilets --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.number_of_toilets') }}</label>
                    <input type="number" name="number_of_toilets" class="form-control" min="1"
                        value="{{ old('number_of_toilets', $unit->number_of_toilets) }}" disabled>
                </div>

                {{-- Unit Area --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_area') }} (m²)</label>
                    <input type="number" step="0.01" name="unit_area" class="form-control"
                        value="{{ old('unit_area', $unit->unit_area) }}" disabled>
                </div>

                {{-- Beds --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.single_beds') }}</label>
                    <input type="number" name="number_of_single_beds" class="form-control" min="0"
                        value="{{ old('number_of_single_beds', $unit->number_of_single_beds) }}" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.double_beds') }}</label>
                    <input type="number" name="number_of_double_beds" class="form-control" min="0"
                        value="{{ old('number_of_double_beds', $unit->number_of_double_beds) }}" disabled>
                </div>

                {{-- Can Be Merged --}}
                <div class="col-md-3 mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="can_be_merged" value="1"
                            {{ old('can_be_merged', $unit->can_be_merged) ? 'checked' : '' }} disabled>
                        <label class="form-check-label">{{ __('dashboard.can_be_merged') }}</label>
                    </div>
                </div>

                {{-- Base Occupancy --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.base_occupancy') }}</label>
                    <input type="number" name="base_occupancy" class="form-control" min="1"
                        value="{{ old('base_occupancy', $unit->base_occupancy) }}" disabled>
                </div>

                {{-- Amenities --}}
                @php
                    $unitAmenityIds = old('amenities', $unit->amenities->pluck('id')->toArray());
                @endphp

                <div class="col-md-12">
                    <label class="form-label mb-2">{{ __('dashboard.amenities') }}</label>
                    <div class="row">
                        @foreach ($amenities as $amenity)
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="amenities[]"
                                        value="{{ $amenity->id }}"
                                        {{ in_array($amenity->id, $unitAmenityIds) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">
                                        {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Description --}}
                <div class="col-md-12">
                    <label class="form-label">{{ __('dashboard.description') }}</label>
                    <textarea name="description" class="form-control" rows="3" disabled>{{ old('description', $unit->description) }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="col-md-12 text-end">
                    <a href="{{ route('setup-sidebar.unit.index') }}" class="btn btn-secondary me-2">


                        {{ __('dashboard.back') }}

                    </a>
                </div>

            </div>
        </form>
    </main>

@endsection
