@extends('layout.master')
@section('title', 'Dashboard | Projects-Tracker')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="text-center mb-4">
                <h1 class="display-4 font-weight-bold ">{{ __('dashboard.all_trackers') }}</h1>

            </div>

            <section class="section">
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div
                                    class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                                    <h4 class="mb-0 text-dark">
                                        <i class="fas fa-tasks mr-2"></i>{{ __('dashboard.trackers') }}
                                    </h4>
                                </div>

                                <div class="card-body px-4 py-4">
                                    @foreach ($projects as $project)
                                        <div class="mb-5"
                                            style="background: #f8f9fa; border-radius: 10px; padding: 20px;">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-primary rounded-circle p-2 mr-3">
                                                    <i class="fas fa-folder text-white"></i>
                                                </div>
                                                <h4 class="mb-0 font-weight-bold">{{ $project->name }}</h4>
                                            </div>

                                            @php
                                                $levels = collect(range(1, 4))->map(function ($level) use ($project) {
                                                    $tracker = $project->trackers->firstWhere('level', $level);
                                                    return (object) [
                                                        'id' => $tracker->id ?? null,
                                                        'level' => $level,
                                                        'description' => $tracker->description ?? null,
                                                        'status' => $tracker->status ?? 'pending',
                                                    ];
                                                });
                                            @endphp

                                            <div class="row">
                                                @foreach ($levels as $tracker)
                                                    <div class="col-md-3 mb-4">
                                                        <div class="card h-100 shadow-sm border-0
                                                            @if ($tracker->status == 'completed') @elseif($tracker->status == 'in_progress')
                                                            @else @endif"
                                                            style="@if ($tracker->status == 'completed') border-left: 4px solid #28a745;
                                                            @elseif($tracker->status == 'in_progress') border-left: 4px solid #17a2b8;
                                                            @else border-left: 4px solid #6c757d; @endif">

                                                            <div
                                                                class="card-header
                                                                @if ($tracker->status == 'completed') bg-success
                                                                @elseif($tracker->status == 'in_progress') bg-info
                                                                @else bg-primary @endif
                                                                text-white border-0">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0 font-weight-bold">
                                                                        <i
                                                                            class="fas fa-layer-group mr-2"></i>{{ __('dashboard.level') }}
                                                                        {{ $tracker->level }}
                                                                    </h6>
                                                                    @if ($tracker->status == 'completed')
                                                                        <i class="fas fa-check-circle"></i>
                                                                    @elseif($tracker->status == 'in_progress')
                                                                        <i class="fas fa-spinner"></i>
                                                                    @else
                                                                        <i class="fas fa-clock"></i>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="card-body bg-white">
                                                                <div class="mb-3">
                                                                    <span
                                                                        class="badge
                                                                        @if ($tracker->status == 'completed') badge-success
                                                                        @elseif($tracker->status == 'in_progress') badge-info
                                                                        @else badge-secondary @endif
                                                                        px-3 py-2">
                                                                        {{ ucfirst(str_replace('_', ' ', $tracker->status)) }}
                                                                    </span>
                                                                </div>

                                                                <div class="small text-muted mb-2">
                                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                                    <strong>{{ __('dashboard.start_date') }}:</strong>
                                                                    {{ $project->start_date ?? '-' }}
                                                                </div>
                                                                <div class="small text-muted mb-3">
                                                                    <i class="far fa-calendar-check mr-1"></i>
                                                                    <strong>{{ __('dashboard.end_date') }}:</strong>
                                                                    {{ $project->end_date ?? '-' }}
                                                                </div>

                                                                <p class="text-muted small mb-0" style="min-height: 60px;">
                                                                    {{ $tracker->description ?? __('dashboard.no_description_available') }}
                                                                </p>
                                                            </div>

                                                            <div class="card-footer bg-light border-0 text-center">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm btn-block"
                                                                    data-toggle="modal"
                                                                    data-target="#trackerModal_{{ $project->id }}_{{ $tracker->level }}">
                                                                    <i
                                                                        class="fas {{ $tracker->id ? 'fa-edit' : 'fa-plus' }} mr-1"></i>
                                                                    {{ $tracker->id ? __('dashboard.edit_tracker') : __('dashboard.add_tracker') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if (!$loop->last)
                                            <hr class="my-5">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- MODALS - Place them at the end, outside table-responsive and other container divs -->
        @foreach ($projects as $project)
            @php
                $levels = collect(range(1, 4))->map(function ($level) use ($project) {
                    $tracker = $project->trackers->firstWhere('level', $level);
                    return (object) [
                        'id' => $tracker->id ?? null,
                        'level' => $level,
                        'description' => $tracker->description ?? null,
                        'status' => $tracker->status ?? 'pending',
                    ];
                });
            @endphp

            @foreach ($levels as $tracker)
                <!-- Modal for {{ $project->name }} - Level {{ $tracker->level }} -->
                <div class="modal fade" id="trackerModal_{{ $project->id }}_{{ $tracker->level }}" tabindex="-1"
                    role="dialog" aria-labelledby="trackerModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <form action="{{ route('dashboard.company.tracker.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <input type="hidden" name="level" value="{{ $tracker->level }}">

                                <div class="modal-header bg-primary text-white border-0">
                                    <h5 class="modal-title" id="trackerModalLabel">
                                        <i class="fas fa-edit mr-2"></i>
                                        {{ __('dashboard.project') }}: {{ $project->name }} — {{ __('dashboard.level') }}
                                        {{ $tracker->level }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body p-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-info-circle mr-1"></i>{{ __('dashboard.status') }}
                                        </label>
                                        <select name="status" class="form-control form-control-lg">
                                            <option value="pending" {{ $tracker->status == 'pending' ? 'selected' : '' }}>
                                                🕐 {{ __('dashboard.pending') }}
                                            </option>
                                            <option value="in_progress"
                                                {{ $tracker->status == 'in_progress' ? 'selected' : '' }}>
                                                ⚙️ {{ __('dashboard.in_progress') }}
                                            </option>
                                            <option value="completed"
                                                {{ $tracker->status == 'completed' ? 'selected' : '' }}>
                                                ✅ {{ __('dashboard.completed') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-align-left mr-1"></i>{{ __('dashboard.description') }}
                                        </label>
                                        <textarea name="description" class="form-control" rows="4" placeholder="{{ __('dashboard.description') }}...">{{ $tracker->description }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">
                                                    <i
                                                        class="far fa-calendar-alt mr-1"></i>{{ __('dashboard.start_date') }}
                                                </label>
                                                <input type="date" class="form-control"
                                                    value="{{ $project->start_date }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">
                                                    <i
                                                        class="far fa-calendar-check mr-1"></i>{{ __('dashboard.end_date') }}
                                                </label>
                                                <input type="date" class="form-control"
                                                    value="{{ $project->end_date }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 bg-light">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i>{{ __('dashboard.cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas {{ $tracker->id ? 'fa-save' : 'fa-plus' }} mr-1"></i>
                                        {{ $tracker->id ? __('dashboard.update_tracker') : __('dashboard.save_tracker') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection
