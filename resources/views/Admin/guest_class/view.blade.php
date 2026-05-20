@extends('layouts.app')

@section('title', 'View Guest Class')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.general_settings') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.view_guest_class') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_view_guest_class_here') }}</div>
            </div>

        </div>

        <div class="container-fluid p-4">
            <form method="post" action="#">
                @csrf
                @method('PUT')

                <!-- Status Toggle -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                            {{ $guestClass->is_active ? 'checked' : '' }} disabled>

                        <label class="form-check-label ms-2" for="is_active">
                            {{ __('dashboard.status') }}
                        </label>
                    </div>
                </div>

                <!-- Class Name -->
                <div class="row mb-3">
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            {{ __('dashboard.class_name') }}
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text" name="class_name" class="form-control"
                            value="{{ $guestClass->class_name ?? '' }}" required disabled>

                    </div>
                </div>

                <!-- Order -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            {{ __('dashboard.order') }}
                            <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="order_no" disabled>
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}"
                                    {{ $guestClass->order_no == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.description') }}
                        </label>

                        <textarea class="form-control" rows="5" name="description" disabled>{{ $guestClass->description ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-3">

                    <a href="{{ route('setup-sidebar.guest_class.index') }}" class="btn btn-outline-danger">
                        {{ __('dashboard.discard') }}
                    </a>

                </div>

            </form>
        </div>


    </main>
@endsection
