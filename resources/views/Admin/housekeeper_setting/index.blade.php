@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.housekeepers_list'))

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
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="page-category">{{ __('dashboard.housekeeping_settings') }}</div>
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.housekeepers_list') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('housekeeper_list.add')
                    <button class="n-button n-button--green" data-bs-toggle="modal" data-bs-target="#createHousekeeperModal">
                        {{ __('dashboard.new_housekeeper') }}
                    </button>
                @endcan

            </div>
        </div>

        <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('setup-sidebar.housekeeping_setting.index') }}">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.select_user') }}</label>
                                <select name="user_id" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->username }}</option>
                                    @endforeach
                                </select>
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
                                <a href="{{ route('setup-sidebar.housekeeping_setting.index') }}"
                                    class="btn btn-outline-secondary">
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
                                <th>{{ __('dashboard.username') }}</th>
                                <th>{{ __('dashboard.employee_name') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.mobile_number') }}</th>
                                <th>{{ __('dashboard.sms_notification') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($housekeepers as $housekeeper)
                                <tr>
                                    <td>{{ $housekeeper->username }}</td>
                                    <td>{{ $housekeeper->employee_name }}</td>
                                    <td>
                                        @if ($housekeeper->is_active)
                                            <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $housekeeper->mobile_number }}</td>
                                    <td>
                                        @if ($housekeeper->sms_notification)
                                            <span class="badge bg-info">{{ __('dashboard.enabled') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('dashboard.disabled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('housekeeper_list.view')
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#viewHousekeeperModal{{ $housekeeper->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan

                                        @can('housekeeper_list.edit')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editHousekeeperModal{{ $housekeeper->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan

                                        @can('housekeeper_list.delete')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteHousekeeperModal{{ $housekeeper->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan

                                    </td>
                                </tr>

                                <div class="modal fade" id="viewHousekeeperModal{{ $housekeeper->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.view_housekeeper') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <span class="switch-label">{{ __('dashboard.inactive') }}</span>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            {{ $housekeeper->is_active ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span class="switch-label">{{ __('dashboard.active') }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('dashboard.user') }} <span
                                                            class="form__star">*</span></label>
                                                    <select class="form-select" disabled>
                                                        <option>{{ $housekeeper->employee_name }}</option>
                                                    </select>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">{{ __('dashboard.full_name') }}</label>
                                                        <div class="fw-bold">{{ $housekeeper->employee_name }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label
                                                            class="form-label">{{ __('dashboard.mobile_number') }}</label>
                                                        <div class="fw-bold">{{ $housekeeper->mobile_number }}</div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            {{ $housekeeper->sms_notification ? 'checked' : '' }} disabled>
                                                    </div>
                                                    <span
                                                        class="switch-label">{{ __('dashboard.enable_sms_notifications') }}</span>
                                                    <div class="switch-label-info">
                                                        {{ __('dashboard.sms_notification_note') }}</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('dashboard.close') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="editHousekeeperModal{{ $housekeeper->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.edit_housekeeper') }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form
                                                action="{{ route('setup-sidebar.housekeeping_setting.update', $housekeeper->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="switch-label">{{ __('dashboard.inactive') }}</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="is_active"
                                                                {{ $housekeeper->is_active ? 'checked' : '' }}>
                                                        </div>
                                                        <span
                                                            class="switch-label">{{ __('dashboard.active_housekeeper') }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.user') }} <span
                                                                class="form__star">*</span></label>
                                                        <select class="form-select" disabled>
                                                            <option>{{ $housekeeper->employee_name }}</option>
                                                        </select>
                                                        <input type="hidden" name="user_id"
                                                            value="{{ $housekeeper->user_id }}">
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label">{{ __('dashboard.full_name') }}</label>
                                                            <div class="fw-bold">{{ $housekeeper->employee_name }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label
                                                                class="form-label">{{ __('dashboard.mobile_number') }}</label>
                                                            <div class="fw-bold">{{ $housekeeper->mobile_number }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="sms_notification"
                                                                {{ $housekeeper->sms_notification ? 'checked' : '' }}>
                                                        </div>
                                                        <span
                                                            class="switch-label">{{ __('dashboard.enable_sms_notifications') }}</span>
                                                        <div class="switch-label-info">
                                                            {{ __('dashboard.sms_notification_note') }}</div>
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

                                <div class="modal fade" id="deleteHousekeeperModal{{ $housekeeper->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ __('dashboard.delete_housekeeper') }} :
                                                    <strong>{{ $housekeeper->employee_name }}</strong>
                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p>{{ __('dashboard.delete_housekeeper_confirmation') }}</p>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('dashboard.cancel') }}
                                                </button>
                                                <form
                                                    action="{{ route('setup-sidebar.housekeeping_setting.destroy', $housekeeper->id) }}"
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
                                    <td colspan="6" class="text-center py-4">
                                        {{ __('dashboard.no_records_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($housekeepers->hasPages())
                <div class="mt-3">
                    {{ $housekeepers->links() }}
                </div>
            @endif

        </div>

        <div class="modal fade" id="createHousekeeperModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.new_housekeeper') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('setup-sidebar.housekeeping_setting.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.user') }} <span
                                        class="form__star">*</span></label>
                                <select class="form-select" name="user_id" required>
                                    <option value="">{{ __('dashboard.select_user') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sms_notification">
                                </div>
                                <span class="switch-label">{{ __('dashboard.enable_sms_notifications') }}</span>
                                <div class="switch-label-info">{{ __('dashboard.sms_notification_note') }}</div>
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
