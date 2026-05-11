@extends('layouts.app')

@section('title', 'Edit Unit')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;"">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.units') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.edit_an_existing_unit') }}</div>
            </div>

        </div>
        <form method="POST" action="{{ route('setup-sidebar.unit.update', $unit->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-md-12 mb-3">
                    <label class="form-label d-block">{{ __('dashboard.status') }}</label>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="active"
                            {{ old('status', $unit->is_active) === true ? 'checked' : '' }}>
                    </div>
                </div>


                {{-- Unit Number --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_number') }}</label>
                    <input type="text" name="unit_number" class="form-control"
                        value="{{ old('unit_number', $unit->unit_number) }}" required>
                </div>

                {{-- Unit Class --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_class') }}</label>
                    <select name="unit_class_id" class="form-select" required>
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
                    <select name="unit_type_id" class="form-select" required>
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
                    <select name="block_id" class="form-select" required>
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
                    <select name="floor_id" class="form-select" required>
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
                    <select name="hall_type_id" class="form-select">
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
                        value="{{ old('phone_extension', $unit->phone_extension) }}">
                </div>

                {{-- Kitchen Type --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.kitchen_type') }}</label>
                    <input type="text" name="kitchen_type" class="form-control"
                        value="{{ old('kitchen_type', $unit->kitchen_type) }}">
                </div>

                {{-- Toilets --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.number_of_toilets') }}</label>
                    <input type="number" name="number_of_toilets" class="form-control" min="1"
                        value="{{ old('number_of_toilets', $unit->number_of_toilets) }}">
                </div>

                {{-- Unit Area --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_area') }} (m²)</label>
                    <input type="number" step="0.01" name="unit_area" class="form-control"
                        value="{{ old('unit_area', $unit->unit_area) }}">
                </div>

                {{-- Beds --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.single_beds') }}</label>
                    <input type="number" name="number_of_single_beds" class="form-control" min="0"
                        value="{{ old('number_of_single_beds', $unit->number_of_single_beds) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.double_beds') }}</label>
                    <input type="number" name="number_of_double_beds" class="form-control" min="0"
                        value="{{ old('number_of_double_beds', $unit->number_of_double_beds) }}">
                </div>

                {{-- Can Be Merged --}}
                <div class="col-md-3 mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="can_be_merged" value="1"
                            {{ old('can_be_merged', $unit->can_be_merged) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('dashboard.can_be_merged') }}</label>
                    </div>
                </div>

                {{-- Base Occupancy --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.base_occupancy') }}</label>
                    <input type="number" name="base_occupancy" class="form-control" min="1"
                        value="{{ old('base_occupancy', $unit->base_occupancy) }}">
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
                                        {{ in_array($amenity->id, $unitAmenityIds) ? 'checked' : '' }}>
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
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $unit->description) }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="col-md-12 text-end">
                    <a href="{{ route('setup-sidebar.unit.index') }}" class="btn btn-secondary me-2">


                        {{ __('dashboard.back') }}

                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.update') }}
                    </button>

                </div>

            </div>
        </form>
    </main>

@endsection
