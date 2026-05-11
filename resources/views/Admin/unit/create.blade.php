@extends('layouts.app')

@section('title', 'Create Unit')

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
                <div class="page-header__subtitle">{{ __('dashboard.create_a_new_unit') }}</div>
            </div>

        </div>
        <form method="POST" action="{{ route('setup-sidebar.unit.store') }}">
            @csrf

            <div class="row g-3">

                <!-- Unit Number -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_number') }}</label>
                    <input type="text" name="unit_number" class="form-control" value="{{ old('unit_number') }}">
                    @error('unit_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit Class -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_class') }}</label>
                    <select name="unit_class_id" class="form-select" >
                        <option value="">{{ __('dashboard.select') }}</option>
                        @foreach ($unitClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                     @error('unit_class_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit Type -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_type') }}</label>
                    <select name="unit_type_id" class="form-select" >
                        <option value="">{{ __('dashboard.select') }}</option>
                        @foreach ($unitTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                     @error('unit_type_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Block -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.block') }}</label>
                    <select name="block_id" class="form-select" >
                        @foreach ($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->name }}</option>
                        @endforeach
                    </select>
                     @error('block_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Floor -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.floor') }}</label>
                    <select name="floor_id" class="form-select" >
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                        @endforeach
                    </select>
                     @error('floor_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hall Type -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.hall_type') }}</label>
                    <select name="hall_type_id" class="form-select">
                        <option value="">{{ __('dashboard.none') }}</option>
                        @foreach ($hallTypes as $hall)
                            <option value="{{ $hall->id }}">{{ $hall->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Phone Extension -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.phone_extension') }}</label>
                    <input type="text" name="phone_extension" class="form-control">
                </div>

                <!-- Kitchen Type -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.kitchen_type') }}</label>
                    <input type="text" name="kitchen_type" class="form-control">
                </div>

                <!-- Toilets -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.number_of_toilets') }}</label>
                    <input type="number" name="number_of_toilets" class="form-control" min="1" value="1">
                     @error('number_of_toilets')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit Area -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.unit_area') }} (m²)</label>
                    <input type="number" step="0.01" name="unit_area" class="form-control">
                </div>

                <!-- Beds -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.single_beds') }}</label>
                    <input type="number" name="number_of_single_beds" class="form-control" min="0">
                     @error('number_of_single_beds')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.double_beds') }}</label>
                    <input type="number" name="number_of_double_beds" class="form-control" min="0">
                     @error('number_of_double_beds')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Toggles -->
                <div class="col-md-3 mt-5">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="can_be_merged" value="1">
                        <label class="form-check-label">{{ __('dashboard.can_be_merged') }}</label>
                    </div>
                </div>

                <!-- Base Occupancy -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('dashboard.base_occupancy') }}</label>
                    <input type="number" name="base_occupancy" class="form-control" min="1" value="1">
                     @error('base_occupancy')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="col-md-12">
                    <label class="form-label mb-2">{{ __('dashboard.amenities') }}</label>

                    <div class="row">
                        @foreach ($amenities as $amenity)
                            <div class="col-md-4">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="amenities[]"
                                        value="{{ $amenity->id }}" id="amenity{{ $amenity->id }}">
                                    <label class="form-check-label" for="amenity{{ $amenity->id }}">
                                        {{ $amenity->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- Description -->
                <div class="col-md-12">
                    <label class="form-label">{{ __('dashboard.description') }}</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>



                <!-- Submit -->
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.save') }}
                    </button>
                </div>

            </div>
        </form>

    </main>

@endsection
