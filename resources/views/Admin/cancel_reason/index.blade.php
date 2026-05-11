@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.cancel_no_show_reason'))

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
        color: {{ $theme->dashboard_card_title_color }};
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

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
        background-color: #1a73e8;
        color: #f8f9fa;
        border-color: {{ $theme->button_primary_bg_color }};
    }

    .n-button--green:hover {
        opacity: 0.9;
    }

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

    .table-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
    }

    .table thead th {
        background: {{ $theme->table_header_bg_color }};
        color: {{ $theme->table_header_text_color }};
        border-color: {{ $theme->table_border_color }};
    }

    .table td {
        border-color: {{ $theme->table_border_color }};
        color: {{ $theme->text_color }};
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="page-category">{{ __('dashboard.rules') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.cancel_no_show_reason') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('cancel_reason.add')
                    <a href="{{ route('setup-sidebar.cancel_reason.create') }}" class="n-button n-button--green"
                        style="text-decoration:none;">
                        {{ __('dashboard.new_reason') }}
                    </a>
                @endcan

            </div>
        </div>

        <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('setup-sidebar.cancel_reason.index') }}">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.description') }}</label>
                                <input type="text" name="description" value="{{ request('description') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_description') }}">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="is_active" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('setup-sidebar.cancel_reason.index') }}"
                                    class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.name') }}</th>
                                <th>{{ __('dashboard.description') }}
                                <th>{{ __('dashboard.penalties') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($cancelReasons as $reason)
                                <tr>
                                    <td>
                                        @if ($reason->is_active)
                                            <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $reason->name }}</td>
                                    <td>{{ $reason->description }}</td>
                                    <td>
                                        {{ $reason->penalties->count() }}
                                    </td>
                                    <td>
                                        @can('cancel_reason.edit')
                                            <a href="{{ route('setup-sidebar.cancel_reason.edit', $reason->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        @can('cancel_reason.delete')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCancelReasonModal{{ $reason->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan


                                        <div class="modal fade" id="deleteCancelReasonModal{{ $reason->id }}"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            {{ __('dashboard.delete_cancel_reason') }} :
                                                            <strong>{{ $reason->name }}</strong>
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p>{{ __('dashboard.delete_cancel_reason_confirmation') }}</p>
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                            {{ __('dashboard.cancel') }}
                                                        </button>
                                                        <form
                                                            action="{{ route('setup-sidebar.cancel_reason.destroy', $reason->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                {{ __('dashboard.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        {{ __('dashboard.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($cancelReasons->hasPages())
                <div class="mt-3">
                    {{ $cancelReasons->links() }}
                </div>
            @endif

        </div>

    </main>
@endsection

@push('scripts')
    <script>
        function toggleFilter() {
            const filterContainer = document.getElementById('filterContainer');
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        }
    </script>
@endpush
