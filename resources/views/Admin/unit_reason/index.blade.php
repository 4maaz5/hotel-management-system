@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', 'Change Unit Reason')

<style>
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

    .n-button--green {
        background-color: {{ $theme->button_primary_bg_color }};
        color: {{ $theme->button_primary_text_color }};
        border-color: {{ $theme->button_primary_bg_color }};
    }

    .filter-form__container {
        background-color: #343a40;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .table-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
    }

    .form__star {
        color: #dc3545;
        margin-left: 3px;
    }

    .switch-label {
        font-weight: 500;
        margin: 0 10px;
    }

    .switch-label--dark {
        color: #333;
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="page-category">{{ __('dashboard.rules') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.change_unit_reason') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('unit_reason.add')
                    <button class="btn btn-primary n-button--blue" data-bs-toggle="modal" data-bs-target="#createUnitReasonModal">
                        {{ __('dashboard.new_change_unit_reason') }}
                    </button>
                @endcan

            </div>
        </div>

        <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('setup-sidebar.unit_reason.index') }}">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
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
                                <a href="{{ route('setup-sidebar.unit_reason.index') }}" class="btn btn-outline-secondary">
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
                                <th>{{ __('dashboard.description') }}</th>
                                <th>{{ __('dashboard.comment_required') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($unitReasons as $reason)
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
                                        @if ($reason->comment_required)
                                            <span class="badge bg-info">{{ __('dashboard.yes') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.no') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('unit_reason.edit')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editUnitReasonModal{{ $reason->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endcan
                                        @can('unit_reason.delete')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteUnitReasonModal{{ $reason->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan

                                    </td>
                                </tr>

                                <div class="modal fade" id="editUnitReasonModal{{ $reason->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.edit_change_unit_reason') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('setup-sidebar.unit_reason.update', $reason->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="u-d-flex justify-content-between mb-3">
                                                        <span class="switch-label">{{ __('dashboard.inactive') }}</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active"
                                                                {{ $reason->is_active ? 'checked' : '' }}>
                                                        </div>
                                                        <span
                                                            class="switch-label switch-label--dark">{{ __('dashboard.active') }}</span>
                                                    </div>
                                                    <div class="u-d-flex justify-content-between mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="comment_required"
                                                                {{ $reason->comment_required ? 'checked' : '' }}>
                                                        </div>
                                                        <span
                                                            class="switch-label">{{ __('dashboard.comment_is_required') }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.name') }} <span
                                                                class="form__star">*</span></label>
                                                        <textarea name="name" class="form-control" rows="3" required>{{ $reason->name }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.description') }}</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $reason->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        {{ __('dashboard.cancel') }}
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('dashboard.save') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteUnitReasonModal{{ $reason->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ __('dashboard.delete_unit_reason') }} :
                                                    <strong>{{ $reason->name }}</strong>
                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p>{{ __('dashboard.delete_unit_reason_confirmation') }}</p>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('dashboard.cancel') }}
                                                </button>
                                                <form
                                                    action="{{ route('setup-sidebar.unit_reason.destroy', $reason->id) }}"
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
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        {{ __('dashboard.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($unitReasons->hasPages())
                <div class="mt-3">
                    {{ $unitReasons->links() }}
                </div>
            @endif

        </div>

        <div class="modal fade" id="createUnitReasonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.new_change_unit_reason') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('setup-sidebar.unit_reason.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="u-d-flex justify-content-between mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="comment_required">
                                </div>
                                <span class="switch-label">{{ __('dashboard.comment_is_required') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.name') }} <span
                                        class="form__star">*</span></label>
                                <textarea name="name" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
