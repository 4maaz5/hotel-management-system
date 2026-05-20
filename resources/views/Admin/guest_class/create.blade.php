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

                <!-- Order -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            {{ __('dashboard.order') }}
                            <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="order_no">
                            <option selected>{{ __('dashboard.select_order') }}</option>
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
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
