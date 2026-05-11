@extends('layouts.app')

@section('title', __('dashboard.night_audit'))
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
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    .n-table__top-btns {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .n-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        text-decoration: none;
        line-height: 1.5;
    }

    .n-button i {
        width: auto;
    }

    .n-button-wrapper {
        display: inline-flex;
        align-items: center;
    }

    .n-button-wrapper form {
        margin: 0;
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

    .filter-form--dark .form-select {
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

    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    .unit-card:hover .card-overlay {
        opacity: 1;
    }

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

        <div class="page-category">
            <h4>{{ __('dashboard.night_audit') }}</h4>
        </div>
        <div class="page-header">
            <div>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_see_and_manage_the_night_audit') }}
                </div>
            </div>
            <div class="n-table__top-btns">
                <div class="n-button-wrapper">
                    <button class="n-button n-button--primary" onclick="toggleFilter()">
                        <i class="fas fa-filter"></i> {{ __('dashboard.filter') }}
                    </button>
                </div>
                @if($settings->is_active)
                    <div class="n-button-wrapper">
                        @can('night_audit.start')
                           <button type="button" class="n-button n-button--green" data-bs-toggle="modal" data-bs-target="#startModal">
                            <i class="fas fa-moon"></i> {{ __('dashboard.start_night_audit') }}
                        </button>
                        @endcan
                    </div>
                @else
                    <span class="badge bg-secondary p-2">
                        <i class="fas fa-lock me-1"></i> {{ __('dashboard.night_audit_disabled') }}
                    </span>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard.night_audit.index') }}">
            <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>
                                        {{ __('dashboard.pending') }}
                                    </option>
                                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>
                                        {{ __('dashboard.completed') }}
                                    </option>
                                    <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>
                                        {{ __('dashboard.failed') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.user') }}</label>
                                <select name="user_id" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.date_from') }}</label>
                                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.date_to') }}</label>
                                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('dashboard.night_audit.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="container py-4">

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.start_date_time') }}</th>
                            <th>{{ __('dashboard.end_date_time') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.user') }}</th>
                            <th>{{ __('dashboard.period_date_from') }}</th>
                            <th>{{ __('dashboard.period_date_to') }}</th>
                            <th>{{ __('dashboard.night_count') }}</th>
                            <th>{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            <tr>
                                <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                                <td>
                                    @if($audit->status === 'completed')
                                        <span class="badge bg-success">{{ __('dashboard.completed') }}</span>
                                    @elseif($audit->status === 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('dashboard.failed') }}</span>
                                    @endif
                                </td>
                                <td>{{ $audit->user->name ?? '-' }}</td>
                                <td>{{ $audit->period_date_from->format('Y-m-d') }}</td>
                                <td>{{ $audit->period_date_to->format('Y-m-d') }}</td>
                                <td>{{ $audit->night_count }}</td>
                                <td>
                                    @if($audit->status === 'completed')
                                    @can('night_audit.download')
                                        <a href="{{ route('dashboard.night_audit.download', $audit->id) }}" class="btn btn-sm btn-primary" title="{{ __('dashboard.download_report') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endcan

                                    @endif
                                    @if($audit->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#completeModal{{ $audit->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#failModal{{ $audit->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @can('night_audit.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $audit->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan

                                </td>
                            </tr>

                            <!-- Complete Modal -->
                            <div class="modal fade" id="completeModal{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('dashboard.complete_night_audit') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ __('dashboard.complete_night_audit_confirm') }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('dashboard.notes') }} ({{ __('dashboard.optional') }})</label>
                                                <textarea name="notes" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                            <form action="{{ route('dashboard.night_audit.complete', $audit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check me-1"></i> {{ __('dashboard.complete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fail Modal -->
                            <div class="modal fade" id="failModal{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('dashboard.fail_night_audit') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ __('dashboard.fail_night_audit_confirm') }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('dashboard.notes') }} ({{ __('dashboard.optional') }})</label>
                                                <textarea name="notes" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                            <form action="{{ route('dashboard.night_audit.fail', $audit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-times me-1"></i> {{ __('dashboard.fail') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('dashboard.delete_night_audit') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>{{ __('dashboard.delete_night_audit_confirm') }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                            <form action="{{ route('dashboard.night_audit.destroy', $audit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash me-1"></i> {{ __('dashboard.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    {{ __('dashboard.no_night_audits_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($audits->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $audits->links() }}
                </div>
            @endif
        </div>

    </main>

    <!-- Start Night Audit Modal -->
    @if($settings->is_active)
    <div class="modal fade" id="startModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.start_night_audit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('dashboard.start_night_audit_confirm') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <form action="{{ route('dashboard.night_audit.start') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-moon me-1"></i> {{ __('dashboard.start') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
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
