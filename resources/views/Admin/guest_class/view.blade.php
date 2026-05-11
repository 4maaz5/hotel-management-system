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

                <!-- Blacklist Switch -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="blacklist" id="blacklist"
                            {{ $guestClass->blacklist ? 'checked' : '' }} disabled>

                        <label class="form-check-label ms-2" for="blacklist">
                            {{ __('dashboard.set_as_blacklist') }}
                        </label>
                    </div>
                </div>

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

                <!-- Order + Icon -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="row">

                            <!-- Order -->
                            <div class="col-6">
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

                            <!-- Icon -->
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.icon') }}
                                </label>

                                <select class="form-select" name="icon" disabled>
                                    <option value="gold" {{ $guestClass->icon == 'gold' ? 'selected' : '' }}>⭐</option>
                                    <option value="blue" {{ $guestClass->icon == 'blue' ? 'selected' : '' }}>⭐</option>
                                    <option value="red" {{ $guestClass->icon == 'red' ? 'selected' : '' }}>⭐</option>
                                    <option value="green" {{ $guestClass->icon == 'green' ? 'selected' : '' }}>⭐</option>
                                    <option value="purple" {{ $guestClass->icon == 'purple' ? 'selected' : '' }}>⭐</option>
                                    <option value="silver" {{ $guestClass->icon == 'silver' ? 'selected' : '' }}>⭐</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Discount -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="row">

                            <div class="col-6">
                                <label class="form-label">
                                    {{ __('dashboard.discount_method') }}
                                </label>

                                <select class="form-select" name="discount_method" disabled>
                                    <option value="amount"
                                        {{ $guestClass->discount_method == 'amount' ? 'selected' : '' }}>
                                        {{ __('dashboard.amount') }}
                                    </option>

                                    <option value="percentage"
                                        {{ $guestClass->discount_method == 'percentage' ? 'selected' : '' }}>
                                        {{ __('dashboard.percentage') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label">
                                    {{ __('dashboard.discount') }}
                                </label>

                                <input type="number" class="form-control" name="discount_amount"
                                    value="{{ $guestClass->discount_amount }}" disabled>
                            </div>

                        </div>
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
