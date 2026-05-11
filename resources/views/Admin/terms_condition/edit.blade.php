@extends('layouts.app')

@section('content')
    <div class="container bg-white p-3" style="border-radius:15px;">
        <h2 class="">{{ __('dashboard.edit_condition') }}</h2>

        <form action="{{ route('setup-sidebar.condition.update', $condition->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3" style="max-width: 400px;">
                <label class="form-label">{{ __('dashboard.order') }}</label>
                <input type="number" name="order_no" class="form-control bg-transparent"
                    value="{{ old('order_no', $condition->order_no) }}" required>

                @error('order_no')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3" style="max-width: 400px;">
                <label class="form-label">{{ __('dashboard.description') }} *</label>
                <textarea name="description" class="form-control bg-transparent" rows="4" maxlength="300" required>{{ old('description', $condition->description) }}</textarea>

                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Status Toggle --}}
            <div class="mb-3" style="max-width: 400px;">
                <label class="form-label">{{ __('dashboard.status') }}</label>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $condition->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label">
                        {{ __('dashboard.active') }}
                    </label>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.update') }}
                </button>

                <a href="{{ route('setup-sidebar.condition.index') }}" class="btn btn-secondary">
                    {{ __('dashboard.cancel') }}
                </a>
            </div>

        </form>
    </div>
@endsection
