@extends('layouts.app')

@section('title', 'Edit Penalty')

@section('content')

    <div class="container bg-white p-3" style="border-radius:15px;">

        <h4 class="mb-4">{{ __('dashboard.penalty_details') }}</h4>

        <form action="{{ route('setup-sidebar.penalty.update', $penalty->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div style="max-width:450px;">

                {{-- Status Toggle --}}
                <div class="mb-3">

                    <label class="form-label">
                        {{ __('dashboard.status') }}
                    </label>

                    <div class="form-check form-switch">

                        <input type="hidden" name="is_active" value="0">

                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            {{ $penalty->is_active ? 'checked' : '' }}>

                    </div>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.name') }} *</label>

                    <input type="text" name="name" class="form-control bg-transparent @error('name') is-invalid @enderror"
                        value="{{ old('name', $penalty->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.category') }} *</label>

                    <select name="category" class="form-control bg-transparent @error('category') is-invalid @enderror" required>

                        @php
                            $categories = [
                                'user_defined' => __('dashboard.user_defined'),
                                'early_checkin' => __('dashboard.early_check_in'),
                                'late_checkout' => __('dashboard.late_check_out'),
                                'no_show' => __('dashboard.cancel_no_show_reservation'),
                            ];
                        @endphp

                        @foreach ($categories as $key => $label)
                            <option class="bg-dark" value="{{ $key }}"
                                {{ old('category', $penalty->category) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Amount --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.amount') }}</label>

                    <input type="number" step="0.01" name="value" class="form-control bg-transparent @error('value') is-invalid @enderror"
                        value="{{ old('value', $penalty->value) }}">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Penalty Type --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.penalty_type') }}</label>

                    <select name="penalty_type" class="form-control bg-transparent @error('penalty_type') is-invalid @enderror">

                        <option class="bg-dark" value="currency"
                            {{ old('penalty_type', $penalty->penalty_type) == 'currency' ? 'selected' : '' }}>
                            SAR
                        </option>

                        <option class="bg-dark" value="percentage"
                            {{ old('penalty_type', $penalty->penalty_type) == 'percentage' ? 'selected' : '' }}>
                            %
                        </option>

                    </select>
                    @error('penalty_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.description') }} *</label>

                    <textarea name="description" rows="4" class="form-control bg-transparent @error('description') is-invalid @enderror"
                        required>{{ old('description', $penalty->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="mt-4">

                    <button class="btn btn-primary px-4">
                        {{ __('dashboard.update') }}
                    </button>

                    <a href="{{ route('setup-sidebar.penalty.index') }}" class="btn btn-secondary px-4">
                        {{ __('dashboard.cancel') }}
                    </a>

                </div>

            </div>
        </form>
    </div>

@endsection
