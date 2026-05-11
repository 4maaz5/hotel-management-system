@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.employee_attendance'))

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

    .table-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="page-category">{{ __('dashboard.housekeeping_settings') }}</div>
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.employee_attendance') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
            </div>
        </div>

        <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('setup-sidebar.staff_attendance.index') }}">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.employee_name') }}</label>
                                <select name="user_id" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($housekeepers as $housekeeper)
                                        <option value="{{ $housekeeper->user_id }}"
                                            {{ request('user_id') == $housekeeper->user_id ? 'selected' : '' }}>
                                            {{ $housekeeper->employee_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>
                                        {{ __('dashboard.checked_in') }}
                                    </option>
                                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>
                                        {{ __('dashboard.checked_out') }}
                                    </option>
                                    <option value="adjusted" {{ request('status') === 'adjusted' ? 'selected' : '' }}>
                                        {{ __('dashboard.adjusted') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.from') }}</label>
                                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.to') }}</label>
                                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('setup-sidebar.staff_attendance.index') }}"
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
                                <th>{{ __('dashboard.employee_name') }}</th>
                                <th>{{ __('dashboard.attendance_date') }}</th>
                                <th>{{ __('dashboard.check_in') }}</th>
                                <th>{{ __('dashboard.check_out') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.geo_location') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->housekeeper?->employee_name ?? $attendance->user?->name }}</td>
                                    <td>{{ optional($attendance->attendance_date)->format('Y-m-d') }}</td>
                                    <td>{{ optional($attendance->check_in_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($attendance->check_out_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status === 'checked_out' ? 'success' : ($attendance->status === 'adjusted' ? 'warning' : 'info') }}">
                                            {{ __('dashboard.' . $attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ __('dashboard.in') }}:
                                            {{ $attendance->check_in_distance_meters !== null ? $attendance->check_in_distance_meters . 'm' : '-' }}
                                            @if ($attendance->check_in_within_geofence !== null)
                                                <span class="badge bg-{{ $attendance->check_in_within_geofence ? 'success' : 'danger' }}">
                                                    {{ $attendance->check_in_within_geofence ? __('dashboard.inside') : __('dashboard.outside') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="small">
                                            {{ __('dashboard.out') }}:
                                            {{ $attendance->check_out_distance_meters !== null ? $attendance->check_out_distance_meters . 'm' : '-' }}
                                            @if ($attendance->check_out_within_geofence !== null)
                                                <span class="badge bg-{{ $attendance->check_out_within_geofence ? 'success' : 'danger' }}">
                                                    {{ $attendance->check_out_within_geofence ? __('dashboard.inside') : __('dashboard.outside') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @can('housekeeper_list.edit')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan

                                        @can('housekeeper_list.delete')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteAttendanceModal{{ $attendance->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>

                                <div class="modal fade" id="editAttendanceModal{{ $attendance->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.edit_employee_attendance') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('setup-sidebar.staff_attendance.update', $attendance->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.check_in') }}</label>
                                                        <input type="datetime-local" name="check_in_at" class="form-control"
                                                            value="{{ optional($attendance->check_in_at)->format('Y-m-d\\TH:i') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.check_out') }}</label>
                                                        <input type="datetime-local" name="check_out_at" class="form-control"
                                                            value="{{ optional($attendance->check_out_at)->format('Y-m-d\\TH:i') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.status') }}</label>
                                                        <select name="status" class="form-select" required>
                                                            @foreach (['checked_in', 'checked_out', 'adjusted'] as $status)
                                                                <option value="{{ $status }}"
                                                                    {{ $attendance->status === $status ? 'selected' : '' }}>
                                                                    {{ __('dashboard.' . $status) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('dashboard.notes') }}</label>
                                                        <textarea name="notes" class="form-control" rows="3">{{ $attendance->notes }}</textarea>
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

                                <div class="modal fade" id="deleteAttendanceModal{{ $attendance->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('dashboard.delete_employee_attendance') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ __('dashboard.delete_employee_attendance_confirmation') }}
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('dashboard.cancel') }}
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('setup-sidebar.staff_attendance.destroy', $attendance->id) }}">
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
                                    <td colspan="7" class="text-muted py-4">{{ __('dashboard.no_data_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </main>

    <script>
        function toggleFilter() {
            const container = document.getElementById('filterContainer');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endsection
