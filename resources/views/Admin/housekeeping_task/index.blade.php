@extends('layouts.app')

@section('title', __('dashboard.housekeeping_tasks'))

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="container mt-4">

            <!-- Page Category -->
            <div class="text-muted fw-semibold mb-2">{{ __('dashboard.housekeeping') }}</div>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">{{ __('dashboard.housekeeping_tasks') }}</h2>
                    <p class="text-muted mb-0">{{ __('dashboard.you_can_view_and_manage_the_housekeeping_tasks') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="openPrintModal()">
                        <i class="bi bi-printer"></i> {{ __('dashboard.print') }}
                    </button>
                    @can('housekeeping_task.add')
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openTaskModal()">
                        <i class="bi bi-plus-circle"></i> {{ __('dashboard.add_task') }}
                    </button>
                    @endcan

                    <button type="button" class="btn btn-outline-secondary" id="toggleFilterBtn">
                        <i class="bi bi-funnel-fill"></i> {{ __('dashboard.filter') }}
                    </button>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('dashboard.housekeeping_task.index') }}" id="filterForm">
                <div class="filter-form__container mb-4" style="display: none;">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.task_type') }}</label>
                            <select name="task_type" class="form-select">
                                <option value="">All</option>
                                <option value="unit" {{ request('task_type') == 'unit' ? 'selected' : '' }}>{{ __('dashboard.unit') }}</option>
                                <option value="property_facility" {{ request('task_type') == 'property_facility' ? 'selected' : '' }}>{{ __('dashboard.property_facility') }}</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.floor') }}</label>
                            <select name="floor_id" class="form-select">
                                <option value="">All</option>
                                @foreach($floors as $floor)
                                    <option value="{{ $floor->id }}" {{ request('floor_id') == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.unit_type') }}</label>
                            <select name="unit_type_id" class="form-select">
                                <option value="">All</option>
                                @foreach($unitTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('unit_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.unit_number') }}</label>
                            <input type="text" name="unit_number" class="form-control" value="{{ request('unit_number') }}">
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('dashboard.pending') }}</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('dashboard.in_progress') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('dashboard.completed') }}</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.priority') }}</label>
                            <select name="priority" class="form-select">
                                <option value="">All</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('dashboard.low') }}</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('dashboard.medium') }}</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('dashboard.high') }}</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.assigned_to') }}</label>
                            <select name="housekeeper_id" class="form-select">
                                <option value="">All</option>
                                @foreach($housekeepers as $housekeeper)
                                    <option value="{{ $housekeeper->id }}" {{ request('housekeeper_id') == $housekeeper->id ? 'selected' : '' }}>
                                        {{ $housekeeper->user->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> {{ __('dashboard.search') }}</button>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="openPrintModal()">
                                <i class="bi bi-printer"></i> {{ __('dashboard.print_preview') }}
                            </button>
                            <a href="{{ route('dashboard.housekeeping_task.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>{{ __('dashboard.created_date') }}</th>
                                    <th>{{ __('dashboard.unit_property_facility') }}</th>
                                    <th>{{ __('dashboard.task_type') }}</th>
                                    <th>{{ __('dashboard.priority') }}</th>
                                    <th>{{ __('dashboard.description') }}</th>
                                    <th>{{ __('dashboard.media') }}</th>
                                    <th>{{ __('dashboard.assigned_to') }}</th>
                                    <th>{{ __('dashboard.task_status') }}</th>
                                    <th>{{ __('dashboard.start_date') }}</th>
                                    <th style="width:120px;">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $index => $task)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $task->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if($task->task_type == 'unit')
                                                {{ $task->unit->unit_number ?? '-' }} - {{ $task->unit->unitType->name ?? '-' }}
                                            @else
                                                {{ $task->propertyFacility->facility->name ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $task->taskType->name ?? '-' }}</td>
                                        <td>
                                            @if($task->priority == 'high')
                                                <span class="badge bg-danger">{{ __('dashboard.high') }}</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning text-dark">{{ __('dashboard.medium') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('dashboard.low') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->description ?? '-' }}</td>
                                        <td>
                                            @if($task->media->count() > 0)
                                                <div class="d-flex gap-1 flex-wrap" style="max-width:150px;">
                                                    @foreach($task->media->take(3) as $media)
                                                        @if($media->file_type === 'image')
                                                            <a href="/storage/{{ $media->file_path }}" target="_blank" title="{{ $media->file_name }}">
                                                                <img src="/storage/{{ $media->file_path }}" alt="{{ $media->file_name }}"
                                                                     style="width:40px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
                                                            </a>
                                                        @else
                                                            <a href="/storage/{{ $media->file_path }}" target="_blank" title="{{ $media->file_name }}">
                                                                <span class="badge bg-secondary" style="font-size:10px;">
                                                                    <i class="bi bi-film"></i>
                                                                </span>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                    @if($task->media->count() > 3)
                                                        <span class="badge bg-secondary" style="font-size:10px;cursor:pointer;"
                                                              onclick="showAllMedia({{ $task->id }})">
                                                            +{{ $task->media->count() - 3 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted" style="font-size:12px;">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->housekeeper->user->name ?? '-' }}</td>
                                        <td>
                                            @if($task->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                            @elseif($task->status == 'in_progress')
                                                <span class="badge bg-info text-dark">{{ __('dashboard.in_progress') }}</span>
                                            @elseif($task->status == 'completed')
                                                <span class="badge bg-success">{{ __('dashboard.completed') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('dashboard.cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->start_date ?? '-' }}</td>
                                        <td class="text-center">
                                            @can('housekeeping_task.edit')
                                                 <button class="btn btn-sm btn-primary" style="padding: 4px 8px;" onclick="editTask({{ $task->id }})">
                                                <i class="bi bi-pencil" style="font-size: 12px;"></i>
                                            </button>
                                             <button class="btn btn-sm btn-success" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#completeTaskModal{{ $task->id }}">
                                                <i class="bi bi-check2" style="font-size: 12px;"></i>
                                            </button>
                                            @endcan

                                           @can('housekeeping_task.delete')
                                              <button class="btn btn-sm btn-danger" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#deleteTaskModal{{ $task->id }}">
                                                <i class="bi bi-trash" style="font-size: 12px;"></i>
                                            </button>
                                           @endcan

                                        </td>
                                    </tr>

                                    <!-- Complete Task Modal -->
                                    <div class="modal fade" id="completeTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">{{ __('dashboard.complete_task') }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p>{{ __('dashboard.complete_task_confirmation') }}</p>
                                                    <strong>{{ $task->taskType->name ?? '-' }} - {{ $task->task_type == 'unit' ? ($task->unit->unit_number ?? '-') : ($task->propertyFacility->name ?? '-') }}</strong>
                                                    @if($task->task_type == 'unit' && stripos(strtolower($task->taskType->name ?? ''), 'deep clean') !== false)
                                                        <div class="alert alert-info mt-2 mb-0">
                                                            <i class="bi bi-info-circle"></i> {{ __('dashboard.deep_clean_unit_update_message') }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        {{ __('dashboard.cancel') }}
                                                    </button>
                                                    <form action="{{ route('dashboard.housekeeping_task.update', $task->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="task_type" value="{{ $task->task_type }}">
                                                        <input type="hidden" name="unit_id" value="{{ $task->unit_id }}">
                                                        <input type="hidden" name="property_facility_id" value="{{ $task->property_facility_id }}">
                                                        <input type="hidden" name="task_type_id" value="{{ $task->task_type_id }}">
                                                        <input type="hidden" name="housekeeper_id" value="{{ $task->housekeeper_id }}">
                                                        <input type="hidden" name="priority" value="{{ $task->priority }}">
                                                        <input type="hidden" name="status" value="completed">
                                                        <input type="hidden" name="description" value="{{ $task->description }}">
                                                        <input type="hidden" name="start_date" value="{{ $task->start_date }}">
                                                        <button type="submit" class="btn btn-success">
                                                            {{ __('dashboard.complete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Task Modal -->
                                    <div class="modal fade" id="deleteTaskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">{{ __('dashboard.delete_task') }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p>{{ __('dashboard.delete_task_confirmation') }}</p>
                                                    <strong>{{ $task->taskType->name ?? '-' }} - {{ $task->task_type == 'unit' ? ($task->unit->unit_number ?? '-') : ($task->propertyFacility->name ?? '-') }}</strong>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        {{ __('dashboard.cancel') }}
                                                    </button>
                                                    <form action="{{ route('dashboard.housekeeping_task.destroy', $task->id) }}" method="POST">
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
                                        <td colspan="11" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-2"></i>
                                            {{ __('dashboard.no_records_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $tasks->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title" id="taskModalTitle">{{ __('dashboard.add_task') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm" method="POST" action="{{ route('dashboard.housekeeping_task.store') }}">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="task_id" id="taskId">

                        <!-- Task Type Toggle -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.task_type_source') }}</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="task_type" id="taskTypeUnit" value="unit" checked onchange="toggleTaskType()">
                                <label class="btn btn-outline-primary" for="taskTypeUnit">{{ __('dashboard.unit') }}</label>

                                <input type="radio" class="btn-check" name="task_type" id="taskTypeFacility" value="property_facility" onchange="toggleTaskType()">
                                <label class="btn btn-outline-primary" for="taskTypeFacility">{{ __('dashboard.property_facility') }}</label>
                            </div>
                        </div>

                        <!-- Unit Dropdown -->
                        <div class="mb-3" id="unitSection">
                            <label class="form-label">{{ __('dashboard.select_unit') }}</label>
                            <select name="unit_id" id="unitSelect" class="form-select">
                                <option value="">{{ __('dashboard.select_unit') }}</option>
                            </select>
                        </div>

                        <!-- Property Facility Dropdown -->
                        <div class="mb-3" id="facilitySection" style="display: none;">
                            <label class="form-label">{{ __('dashboard.select_facility') }}</label>
                            <select name="property_facility_id" id="facilitySelect" class="form-select">
                                <option value="">{{ __('dashboard.select_facility') }}</option>
                                @foreach($propertyFacilities as $facility)
                                    <option value="{{ $facility->id }}">{{ $facility->facility->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.task_type') }} *</label>
                            <select name="task_type_id" id="taskTypeSelect" class="form-select" required>
                                <option value="">{{ __('dashboard.select_task_type') }}</option>
                                @foreach($taskTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.priority') }} *</label>
                            <select name="priority" id="prioritySelect" class="form-select" required>
                                <option value="low">{{ __('dashboard.low') }}</option>
                                <option value="medium" selected>{{ __('dashboard.medium') }}</option>
                                <option value="high">{{ __('dashboard.high') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.assigned_to') }}</label>
                            <select name="housekeeper_id" id="housekeeperSelect" class="form-select">
                                <option value="">{{ __('dashboard.select_housekeeper') }}</option>
                                @foreach($housekeepers as $housekeeper)
                                    <option value="{{ $housekeeper->id }}">{{ $housekeeper->user->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.start_date') }}</label>
                            <input type="date" name="start_date" id="startDate" class="form-select" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.status') }}</label>
                            <select name="status" id="statusSelect" class="form-select">
                                <option value="pending">{{ __('dashboard.pending') }}</option>
                                <option value="in_progress">{{ __('dashboard.in_progress') }}</option>
                                <option value="completed">{{ __('dashboard.completed') }}</option>
                                <option value="cancelled">{{ __('dashboard.cancelled') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.description') }}</label>
                            <textarea name="description" id="descriptionText" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitTask()">{{ __('dashboard.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Media Modal -->
    <div class="modal fade" id="taskMediaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">{{ __('dashboard.media') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="taskMediaContent">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('en')">
                            English
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('ar')">
                            العربية
                        </button>
                        @if(optional($printingOption)->contract_template_type == 'double')
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('both')">
                            Both
                        </button>
                        @endif
                    </div>
                    <div class="btn-group ms-3" role="group">
                        <button type="button" class="btn btn-primary" id="printBtn" onclick="printPage()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="printIframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const housekeepingTaskRoutes = {
            index: @json(route('dashboard.housekeeping_task.index', [], false)),
            units: @json(route('dashboard.housekeeping_task.get_units', [], false)),
        };

        function housekeepingTaskUrl(path = '') {
            return `${housekeepingTaskRoutes.index}${path}`;
        }

        // Toggle filter
        document.getElementById('toggleFilterBtn').addEventListener('click', function() {
            const filterContainer = document.querySelector('.filter-form__container');
            if (filterContainer.style.display === 'none' || filterContainer.style.display === '') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        // Show filter form if there are active filters
        @if(request()->hasAny(['task_type', 'floor_id', 'unit_type_id', 'unit_number', 'status', 'priority', 'housekeeper_id']))
            document.querySelector('.filter-form__container').style.display = 'block';
        @endif

        // Print Modal Functions
        function openPrintModal() {
            const printUrl = '{{ route("dashboard.housekeeping_task.print") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString();
            document.getElementById('printIframe').src = printUrl;
            var modal = new bootstrap.Modal(document.getElementById('printModal'));
            modal.show();

            // Handle iframe load
            document.getElementById('printIframe').onload = function() {
                setTimeout(function() {
                    switchPrintLang('en');
                }, 500);
            };
        }

        function switchPrintLang(lang) {
            console.log('Switching language to:', lang);
            const iframe = document.getElementById('printIframe');
            try {
                if (iframe && iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                    console.log('Calling iframe switchLanguage');
                    iframe.contentWindow.switchLanguage(lang);
                } else {
                    console.log('Iframe not ready, retrying...');
                    setTimeout(function() {
                        if (iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                            iframe.contentWindow.switchLanguage(lang);
                        }
                    }, 500);
                }
            } catch(e) {
                console.log('Error:', e);
            }
        }

        function printPage() {
            const iframe = document.querySelector('#printIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        }

        // Task type toggle
        function toggleTaskType() {
            const taskType = document.querySelector('input[name="task_type"]:checked').value;
            if (taskType === 'unit') {
                document.getElementById('unitSection').style.display = 'block';
                document.getElementById('facilitySection').style.display = 'none';
                document.getElementById('unitSelect').required = true;
                document.getElementById('facilitySelect').required = false;
            } else {
                document.getElementById('unitSection').style.display = 'none';
                document.getElementById('facilitySection').style.display = 'block';
                document.getElementById('unitSelect').required = false;
                document.getElementById('facilitySelect').required = true;
            }
        }

        // Open modal for new task
        function openTaskModal() {
            document.getElementById('taskModalTitle').textContent = '{{ __('dashboard.add_task') }}';
            document.getElementById('taskForm').action = '{{ route('dashboard.housekeeping_task.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('taskId').value = '';
            document.getElementById('taskForm').reset();
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('taskTypeUnit').checked = true;
            toggleTaskType();
        }

        // Submit form
        function submitTask() {
            const form = document.getElementById('taskForm');
            form.submit();
        }

        // Edit task
        function editTask(taskId) {
            fetch(housekeepingTaskUrl(`/${taskId}`), {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('taskModalTitle').textContent = '{{ __('dashboard.edit_task') }}';
                    document.getElementById('taskForm').action = housekeepingTaskUrl(`/${taskId}`);
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('taskId').value = taskId;

                    // Set task type
                    if (data.task_type === 'unit') {
                        document.getElementById('taskTypeUnit').checked = true;
                    } else {
                        document.getElementById('taskTypeFacility').checked = true;
                    }
                    toggleTaskType();

                    // Set values
                    document.getElementById('unitSelect').value = data.unit_id || '';
                    document.getElementById('facilitySelect').value = data.property_facility_id || '';
                    document.getElementById('taskTypeSelect').value = data.task_type_id || '';
                    document.getElementById('prioritySelect').value = data.priority || 'medium';
                    document.getElementById('housekeeperSelect').value = data.housekeeper_id || '';
                    document.getElementById('statusSelect').value = data.status || 'pending';
                    document.getElementById('startDate').value = data.start_date || '';
                    document.getElementById('descriptionText').value = data.description || '';

                    var modal = new bootstrap.Modal(document.getElementById('taskModal'));
                    modal.show();
                });
        }

        // Complete task
        function completeTask(taskId) {
            if (confirm('{{ __('dashboard.confirm_complete_task') }}')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = housekeepingTaskUrl(`/${taskId}`);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'completed';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';

                form.appendChild(methodInput);
                form.appendChild(statusInput);
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete task
        function deleteTask(taskId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = housekeepingTaskUrl(`/${taskId}`);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            form.appendChild(methodInput);
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Load units
        function loadUnits() {
            fetch(housekeepingTaskRoutes.units, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(units => {
                    const select = document.getElementById('unitSelect');
                    select.innerHTML = '<option value="">{{ __('dashboard.select_unit') }}</option>';
                    units.forEach(unit => {
                        select.innerHTML += `<option value="${unit.id}">${unit.unit_number} - ${unit.unit_type?.name || ''}</option>`;
                    });
                });
        }

        // Show all media for a task
        function showAllMedia(taskId) {
            fetch(housekeepingTaskUrl(`/${taskId}`), {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    const task = data.task || data;
                    const mediaList = task.media || [];
                    let html = '';
                    mediaList.forEach(m => {
                        const src = '/storage/' + (m.file_path || '');
                        if (m.file_type === 'image') {
                            html += `<a href="${src}" target="_blank" class="d-inline-block m-1">
                                <img src="${src}" alt="${m.file_name || ''}"
                                     style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                            </a>`;
                        } else {
                            html += `<a href="${src}" target="_blank" class="d-inline-block m-1 text-center p-3 border rounded"
                                     style="width:100px;height:100px;background:#f8f9fa;">
                                <i class="bi bi-film" style="font-size:28px;"></i><br>
                                <small style="font-size:10px;word-break:break-all;">${m.file_name || 'video'}</small>
                            </a>`;
                        }
                    });
                    document.getElementById('taskMediaContent').innerHTML = html || '<p class="text-muted">No media</p>';
                    var modal = new bootstrap.Modal(document.getElementById('taskMediaModal'));
                    modal.show();
                });
        }

        // Load units on page load
        loadUnits();
    </script>
@endpush
