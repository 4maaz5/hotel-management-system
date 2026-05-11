@extends('layouts.app')

@section('title', 'Create Guest Class')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.general_settings') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.guest_class') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_see_and_manage_guest_classes') }}</div>
            </div>

        </div>

        <div class="container-fluid p-4">

            <form method="post" action="{{ route('setup-sidebar.guest_class.store') }}">
                @csrf
                <!-- Blacklist Switch -->
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="blacklist" name="blacklist">

                        <label class="form-check-label ms-2" for="blacklist">
                            {{ __('dashboard.set_as_blacklist') }}
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
                            placeholder="{{ __('dashboard.type_class_name') }}" required>


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

                                <select class="form-select" name="order_no">

                                    <option selected>
                                        {{ __('dashboard.select_order') }}
                                    </option>

                                    @for ($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor

                                </select>

                            </div>

                            <!-- Icons -->
                            <div class="col-6">

                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.icon') }}
                                </label>

                                <select class="form-select" name="icon">

                                    <option selected>
                                        {{ __('dashboard.pick_icon') }}
                                    </option>
                                    <option value="gold">⭐</option>
                                    <option value="blue">⭐</option>
                                    <option value="red">⭐</option>
                                    <option value="green">⭐</option>
                                    <option value="purple">⭐</option>
                                    <option value="silver">⭐</option>

                                </select>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Discount Method + Amount -->
                <div class="row mb-3">
                    <div class="col-md-3">

                        <div class="row">

                            <div class="col-6">

                                <label class="form-label">
                                    {{ __('dashboard.discount_method') }}
                                </label>

                                <select class="form-select" name="discount_method">
                                    <option value="">
                                        {{ __('dashboard.select_method') }}
                                    </option>
                                    <option value="amount">
                                        {{ __('dashboard.amount') }}
                                    </option>
                                    <option value="percentage">
                                        {{ __('dashboard.percentage') }}
                                    </option>
                                </select>

                            </div>

                            <div class="col-6">

                                <label class="form-label">
                                    {{ __('dashboard.discount') }}
                                </label>

                                <input type="number" class="form-control"
                                    placeholder="{{ __('dashboard.enter_discount') }}" name="discount_amount">
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

                        <textarea class="form-control" rows="5" name="description" placeholder="{{ __('dashboard.type_description') }}"></textarea>

                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-3">

                    <a href="{{ route('setup-sidebar.guest_class.index') }}" type="button" class="btn btn-outline-danger">
                        {{ __('dashboard.discard') }}
                    </a>

                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.create_guest_class') }}
                    </button>

                </div>

            </form>
        </div>


    </main>
@endsection
