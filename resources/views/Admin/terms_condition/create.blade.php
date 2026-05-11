@extends('layouts.app')

@section('content')
    <div class="container bg-white p-3" style="border-radius:15px;">
        <h2 class="">{{ __('dashboard.condition_details') }}</h2>

        <form action="{{ route('setup-sidebar.condition.store') }}" method="POST">
            @csrf

            <div class="mb-3" style="max-width: 400px;">
                <label class="form-label">{{ __('dashboard.order') }}</label>
                <input type="number" name="order_no" class="form-control bg-transparent"
                    value="{{ old('order_no', $maxOrder + 1) }}" required>

                @error('order_no')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3" style="max-width: 400px;">
                <label class="form-label">{{ __('dashboard.description') }} *</label>
                <textarea name="description" class="form-control bg-transparent" rows="4" maxlength="300" required>{{ old('description') }}</textarea>

                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.save') }}
                </button>

                <a href="{{ route('setup-sidebar.condition.index') }}" class="btn btn-secondary">
                    {{ __('dashboard.cancel') }}
                </a>
            </div>

        </form>
    </div>
@endsection
