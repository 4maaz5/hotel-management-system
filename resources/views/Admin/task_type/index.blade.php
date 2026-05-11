@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', 'Housekeeping Task type')

<style>
    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
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

    .table-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
    }

    .switch-label {
        font-weight: 500;
        margin: 0 10px;
    }

    .switch-label-info {
        font-size: 0.8rem;
        color: #6c757d;
        margin-left: 28px;
    }

    .form__star {
        color: #dc3545;
    }

    .dropdown-menu {
        z-index: 1050;
    }

    .table-card {
        overflow: visible;
    }

    .table-card .table {
        overflow: visible;
    }

    .table-responsive {
        overflow: visible !important;
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="page-category">{{ __('dashboard.housekeeping_settings') }}</div>
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.housekeeping_task_type') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('housekeeper_task.add')
                    <button class="n-button n-button--green" data-bs-toggle="modal" data-bs-target="#createTaskTypeModal">
                        {{ __('dashboard.add_housekeeping_task_type') }}
                    </button>
                @endcan

            </div>
        </div>

        <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('setup-sidebar.task_type.index') }}">
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
                                <a href="{{ route('setup-sidebar.task_type.index') }}" class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('dashboard.task_type') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.routine') }}</th>
                                <th style="width:80px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taskTypes as $taskType)
                                <tr>
                                    <td>{{ $taskType->name }}</td>
                                    <td>
                                        @if ($taskType->is_active)
                                            <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($taskType->is_routine)
                                            <span class="badge bg-info">{{ __('dashboard.yes') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.no') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            @can('housekeeper_task.edit')
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editTaskTypeModal{{ $taskType->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan

                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <form
                                                            action="{{ route('setup-sidebar.task_type.toggle', $taskType->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @can('housekeeper_task.status')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-power-off me-2"></i>
                                                                    {{ $taskType->is_active ? __('dashboard.deactivate') : __('dashboard.activate') }}
                                                                </button>
                                                            @endcan

                                                        </form>
                                                    </li>
                                                    <li>
                                                        @can('housekeeper_task.delete')
                                                            <button class="dropdown-item text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteTaskTypeModal{{ $taskType->id }}">
                                                                <i class="fas fa-trash me-2"></i> {{ __('dashboard.delete') }}
                                                            </button>
                                                        @endcan

                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editTaskTypeModal{{ $taskType->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.edit_housekeeping_task_type') }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('setup-sidebar.task_type.update', $taskType->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="switch-label">{{ __('dashboard.inactive') }}</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active"
                                                                {{ $taskType->is_active ? 'checked' : '' }}>
                                                        </div>
                                                        <span class="switch-label">{{ __('dashboard.active') }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('dashboard.housekeeping_task_type') }}
                                                            <span class="form__star">*</span></label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $taskType->name }}" disabled>
                                                        <input type="hidden" name="name" value="{{ $taskType->name }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_routine"
                                                                {{ $taskType->is_routine ? 'checked' : '' }}>
                                                        </div>
                                                        <span
                                                            class="switch-label">{{ __('dashboard.set_as_routine_task_type') }}</span>
                                                        <div class="switch-label-info">
                                                            {{ __('dashboard.routine_task_note') }}</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        {{ __('dashboard.discard') }}
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('dashboard.save_changes') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteTaskTypeModal{{ $taskType->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ __('dashboard.delete_task_type') }} :
                                                    <strong>{{ $taskType->name }}</strong>
                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p>{{ __('dashboard.delete_task_type_confirmation') }}</p>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('dashboard.cancel') }}
                                                </button>
                                                <form
                                                    action="{{ route('setup-sidebar.task_type.destroy', $taskType->id) }}"
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

            @if ($taskTypes->hasPages())
                <div class="mt-3">
                    {{ $taskTypes->links() }}
                </div>
            @endif

        </div>

        <div class="modal fade" id="createTaskTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_housekeeping_task_type') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('setup-sidebar.task_type.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.housekeeping_task_type') }} <span
                                        class="form__star">*</span></label>
                                <select class="form-select" name="name" required>
                                    <option value="">{{ __('dashboard.select_task_type') }}</option>
                                    <option value="Unit Deep Clean">{{ __('dashboard.unit_deep_clean') }}</option>
                                    <option value="Unit Touch Up">{{ __('dashboard.unit_touch_up') }}</option>
                                    <option value="Unit Maintenance">{{ __('dashboard.unit_maintenance') }}</option>
                                    <option value="Mattresses Flipping">{{ __('dashboard.mattresses_flipping') }}</option>
                                    <option value="Room Content Check">{{ __('dashboard.room_content_check') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_routine">
                                </div>
                                <span class="switch-label">{{ __('dashboard.set_as_routine_task_type') }}</span>
                                <div class="switch-label-info">{{ __('dashboard.routine_task_note') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
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
