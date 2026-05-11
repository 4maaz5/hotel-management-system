@extends('layouts.app')
@section('title', 'Create Penalty')
@section('content')
    <div class="container bg-white p-3" style="border-radius:15px;">

        <h4 class="mb-4">{{ __('dashboard.penalty_details') }}</h4>

        <form action="{{ route('setup-sidebar.penalty.store') }}" method="POST">
            @csrf

            <div style="max-width:450px;">

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.name') }} *
                    </label>

                    <input type="text" name="name" class="form-control bg-transparent @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.category') }} *
                    </label>

                    <select name="category" class="form-control bg-white text-dark @error('category') is-invalid @enderror" required>
                        <option value="">{{ __('dashboard.select_category') }}</option>
                        <option value="user_defined" {{ old('category') == 'user_defined' ? 'selected' : '' }}>{{ __('dashboard.user_defined') }}</option>
                        <option value="early_checkin" {{ old('category') == 'early_checkin' ? 'selected' : '' }}>{{ __('dashboard.early_check_in') }}</option>
                        <option value="late_checkout" {{ old('category') == 'late_checkout' ? 'selected' : '' }}>{{ __('dashboard.late_check_out') }}</option>
                        <option value="no_show" {{ old('category') == 'no_show' ? 'selected' : '' }}>{{ __('dashboard.cancel_no_show_reservation') }}</option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Amount --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.amount') }}
                    </label>

                    <input type="number" step="0.01" name="value" class="form-control bg-transparent @error('value') is-invalid @enderror"
                        value="{{ old('value') }}">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Penalty Type --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.penalty_type') }}
                    </label>

                    <select name="penalty_type" class="form-control bg-transparent @error('penalty_type') is-invalid @enderror">

                        <option class="bg-dark" value="currency" {{ old('penalty_type', 'currency') == 'currency' ? 'selected' : '' }}>SAR</option>
                        <option class="bg-dark" value="percentage" {{ old('penalty_type') == 'percentage' ? 'selected' : '' }}>%</option>

                    </select>
                    @error('penalty_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.description') }} *
                    </label>

                    <textarea name="description" rows="4" class="form-control bg-transparent @error('description') is-invalid @enderror"
                        required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="mt-4">
                    <button class="btn btn-primary px-4">
                        {{ __('dashboard.save') }}
                    </button>

                    <a href="{{ route('setup-sidebar.penalty.index') }}" class="btn btn-secondary px-4">
                        {{ __('dashboard.cancel') }}
                    </a>
                </div>

            </div>

        </form>
    </div>
@endsection
