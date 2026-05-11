@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.guest_feedback_metrics'))

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

    .table-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
    }

    .switch-label {
        font-weight: 500;
        margin: 0 10px;
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="container mt-4">

            <div class="page-category">
                {{ __('dashboard.rules') }}
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-header__title mb-0">{{ __('dashboard.guest_feedback_metrics') }}</h4>
                @can('feedback_metric.add')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMetricModal">
                        <i class="fas fa-plus me-1"></i> {{ __('dashboard.append_guest_feedback_metric') }}
                    </button>
                @endcan

            </div>

            <div class="table-card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                    <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metrics as $metric)
                                    <tr>
                                        <td>{{ $metric->name }}</td>
                                        <td>
                                            @if ($metric->is_active)
                                                <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('dashboard.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('feedback_metric.edit')
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editMetricModal{{ $metric->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endcan
                                            @can('feedback_metric.delete')
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteMetricModal{{ $metric->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan

                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editMetricModal{{ $metric->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ __('dashboard.edit_guest_feedback_metric') }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <form
                                                    action="{{ route('setup-sidebar.guest_feedback.update', $metric->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span
                                                                class="switch-label">{{ __('dashboard.inactive') }}</span>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_active"
                                                                    {{ $metric->is_active ? 'checked' : '' }}>
                                                            </div>
                                                            <span class="switch-label">{{ __('dashboard.active') }}</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('dashboard.name') }}</label>
                                                            <select class="form-select" disabled>
                                                                <option>{{ $metric->name }}</option>
                                                            </select>
                                                            <input type="hidden" name="name"
                                                                value="{{ $metric->name }}">
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

                                    <div class="modal fade" id="deleteMetricModal{{ $metric->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ __('dashboard.delete_guest_feedback_metric') }} :
                                                        <strong>{{ $metric->name }}</strong>
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p>{{ __('dashboard.delete_guest_feedback_metric_confirmation') }}</p>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        {{ __('dashboard.cancel') }}
                                                    </button>
                                                    <form
                                                        action="{{ route('setup-sidebar.guest_feedback.destroy', $metric->id) }}"
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
                                        <td colspan="3" class="text-center py-4">
                                            {{ __('dashboard.no_records_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($metrics->hasPages())
                <div class="mt-3">
                    {{ $metrics->links() }}
                </div>
            @endif

        </div>

        <div class="modal fade" id="createMetricModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.append_guest_feedback_metric') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('setup-sidebar.guest_feedback.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.name') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="name" required>
                                    <option value="">{{ __('dashboard.select_metric') }}</option>
                                    @foreach ($metricOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
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
