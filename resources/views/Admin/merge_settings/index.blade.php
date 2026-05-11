@extends('layouts.app')

@section('title', 'Merge Setting')

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

    /* Table Top Buttons */
    .n-table__top-btns {
        display: flex;
        gap: 0.75rem;
    }

    .n-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .n-button--primary {
        background-color: white;
        color: #333;
        border-color: #dee2e6;
    }

    .n-button--primary:hover {
        background-color: #f8f9fa;
        border-color: #4a90e2;
    }

    .n-button--green {
        background-color: #2335da;
        color: white;
        border-color: #190cd8;
    }

    .n-button--green:hover {
        background-color: #3759f1;
        border-color: #292ce9;
    }

    /* Filter Form */
    .filter-form__container {
        background-color: #343a40;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-form {
        padding: 1.5rem;
    }

    .filter-form--dark label {
        color: #e9ecef;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control::placeholder {
        color: #adb5bd;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    /* Overlay hidden by default */
    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* semi-transparent overlay */
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    /* Show overlay on hover */
    .unit-card:hover .card-overlay {
        opacity: 1;
    }

    /* Style buttons */
    .unit-card .card-overlay .btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unit-card .card-overlay .btn i {
        font-size: 16px;
    }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.new_unit_merge_setting') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_merge_setting') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('unit.add')
                        <a href="{{ route('setup-sidebar.merge_setting.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_unit_merge') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Form for Unit Number -->
        <form method="GET" action="{{ route('setup-sidebar.merge_setting.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Unit Number Filter -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.unit_number') }}</label>
                                <input type="text" name="unit_number" value="{{ request('unit_number') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_unit_no') }}">
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-2 col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('setup-sidebar.merge_setting.index') }}"
                                    class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div class="container mt-4">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('dashboard.merge_code') }}</th>
                        <th>{{ __('dashboard.unit_classes') }}</th>
                        <th>{{ __('dashboard.unit_types') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unitMerges as $unitMerge)
                        <tr>
                            {{-- Merge Code --}}
                            <td>{{ $unitMerge->merge_code }}</td>

                            {{-- Unit Classes (show both units) --}}
                            <td>
                                {{ $unitMerge->unitA->unitClass->name ?? '-' }} /
                                {{ $unitMerge->unitB->unitClass->name ?? '-' }}
                            </td>

                            {{-- Unit Types (show both units) --}}
                            <td>
                                {{ $unitMerge->unitA->unitType->name ?? '-' }} /
                                {{ $unitMerge->unitB->unitType->name ?? '-' }}
                            </td>

                            {{-- Actions --}}
                            <td>
                                @can('merge_setting.delete')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deactivateUserModal{{ $unitMerge->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>

                        {{-- Delete Modal for this merge --}}
                        <div class="modal fade" id="deactivateUserModal{{ $unitMerge->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('dashboard.delete_unit_merge_setting') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('dashboard.delete_merge_setting_confirmation') }}</p>
                                        <hr>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">{{ __('dashboard.merge_code') }}</dt>
                                            <dd class="col-sm-7">{{ $unitMerge->merge_code }}</dd>

                                            <dt class="col-sm-5">{{ __('dashboard.unit_a') }}</dt>
                                            <dd class="col-sm-7">{{ $unitMerge->unitA->unit_number ?? '-' }}
                                                ({{ $unitMerge->unitA->unitClass->name ?? '-' }})
                                            </dd>

                                            <dt class="col-sm-5">{{ __('dashboard.unit_b') }}</dt>
                                            <dd class="col-sm-7">{{ $unitMerge->unitB->unit_number ?? '-' }}
                                                ({{ $unitMerge->unitB->unitClass->name ?? '-' }})</dd>

                                            <dt class="col-sm-5">{{ __('dashboard.description') }}</dt>
                                            <dd class="col-sm-7">{{ ucfirst($unitMerge->unitA->description ?? '-') }}</dd>
                                        </dl>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                        <form action="{{ route('setup-sidebar.merge_setting.destroy', $unitMerge->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4">{{ __('dashboard.no_merge_setting_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </main>


@endsection
@push('scripts')
    <script>
        const toggleBtn = document.querySelector('.n-button.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });
    </script>
@endpush
