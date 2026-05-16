@extends('layouts.app')

@section('title', __('dashboard.housekeeping_status'))

@section('content')
    <main class="u-white-bg bg-white p-2" style="border-radius:10px;">
        <div class="container mt-4">
            <!-- Page Category -->
            <div class="text-muted fw-semibold mb-2">{{ __('dashboard.housekeeping') }}</div>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">{{ __('dashboard.housekeeping_status') }}</h2>
                    <p class="text-muted mb-0">{{ __('dashboard.you_can_view_and_manage_the_housekeeping_status') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="openPrintModal()">
                        <i class="bi bi-printer"></i> {{ __('dashboard.print') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="toggleFilterBtn">
                        <i class="bi bi-funnel-fill"></i> {{ __('dashboard.filter') }}
                    </button>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('dashboard.housekeeping_status.index') }}" id="filterForm">
                <div class="filter-form__container mb-4" style="display: none;">
                    <div class="row g-3 mb-3">
                        <div class="col-lg-2 col-md-4">
                            <label>{{ __('dashboard.hk_status') }}</label>
                            <select name="housekeeping_status" class="form-select">
                                <option value="">All</option>
                                <option value="clean" {{ request('housekeeping_status') == 'clean' ? 'selected' : '' }}>{{ __('dashboard.clean') }}</option>
                                <option value="dirty" {{ request('housekeeping_status') == 'dirty' ? 'selected' : '' }}>{{ __('dashboard.dirty') }}</option>
                                <option value="inspected" {{ request('housekeeping_status') == 'inspected' ? 'selected' : '' }}>{{ __('dashboard.inspected') }}</option>
                                <option value="out_of_service" {{ request('housekeeping_status') == 'out_of_service' ? 'selected' : '' }}>{{ __('dashboard.out_of_service') }}</option>
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
                            <input type="text" name="unit_number" class="form-control" placeholder="{{ __('dashboard.enter_unit_number') }}" value="{{ request('unit_number') }}">
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> {{ __('dashboard.search') }}</button>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="printWithFilters()">
                            <i class="bi bi-printer"></i> {{ __('dashboard.print_preview') }}
                        </button>
                        <a href="{{ route('dashboard.housekeeping_status.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
                    </div>
                </div>
            </form>

            <!-- Status Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0" style="background-color: #fee2e2;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-broom" style="font-size: 2rem; color: #dc2626;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">{{ $dirtyCount }}</h4>
                                <small class="text-muted">{{ __('dashboard.dirty') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="background-color: #d1fae5;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-check-circle" style="font-size: 2rem; color: #059669;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">{{ $cleanCount }}</h4>
                                <small class="text-muted">{{ __('dashboard.clean') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="background-color: #dbeafe;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-clipboard-check" style="font-size: 2rem; color: #2563eb;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">{{ $inspectedCount }}</h4>
                                <small class="text-muted">{{ __('dashboard.inspected') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0" style="background-color: #f3f4f6;">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-tools" style="font-size: 2rem; color: #6b7280;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">{{ $outOfServiceCount }}</h4>
                                <small class="text-muted">{{ __('dashboard.out_of_service') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Housekeeping Table -->
            <div class="table-responsive bg-white">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>

                            <th>{{ __('dashboard.unit_no') }}.<br>{{ __('dashboard.unit_type') }}</th>
                            <th>{{ __('dashboard.hk_status') }}</th>
                            <th>{{ __('dashboard.occupancy_status') }}</th>
                            <th>{{ __('dashboard.notes') }}</th>
                            <th>{{ __('dashboard.do_not_disturb') }}</th>
                            <th>{{ __('dashboard.check_in') }}</th>
                            <th>{{ __('dashboard.check_out') }}</th>
                            <th>{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unitsWithStatus as $unit)
                            <tr data-unit-id="{{ $unit->id }}" data-hk-status="{{ $unit->housekeeping_status }}">

                                <td>
                                    <strong>{{ $unit->unit_number }}</strong><br>
                                    <small class="text-muted">{{ $unit->unitType->name ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($unit->housekeeping_status == 'clean')
                                        <span class="badge bg-success">{{ __('dashboard.clean') }}</span>
                                    @elseif($unit->housekeeping_status == 'dirty')
                                        <span class="badge bg-warning text-dark">{{ __('dashboard.dirty') }}</span>
                                    @elseif($unit->housekeeping_status == 'inspected')
                                        <span class="badge bg-info">{{ __('dashboard.inspected') }}</span>
                                    @elseif($unit->housekeeping_status == 'out_of_service')
                                        <span class="badge bg-secondary">{{ __('dashboard.out_of_service') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($unit->occupancy_status == 'vacant')
                                        <span class="badge bg-success">{{ __('dashboard.vacant') }}</span>
                                    @elseif($unit->occupancy_status == 'occupied')
                                        <span class="badge bg-danger">{{ __('dashboard.occupied') }}</span>
                                    @elseif($unit->occupancy_status == 'check_out_today')
                                        <span class="badge bg-warning text-dark">{{ __('dashboard.check_out_today') }}</span>
                                    @endif
                                </td>
                                <td>{{ $unit->notes ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">No</span>
                                </td>
                                <td>{{ $unit->check_in_date ?? '-' }}</td>
                                <td>{{ $unit->check_out_date ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm me-1"
                                            onclick="markAsClean({{ $unit->id }})"
                                            title="{{ __('dashboard.mark_as_clean') }}">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm"
                                            onclick="editStatus({{ $unit->id }}, '{{ $unit->housekeeping_status }}')"
                                            title="{{ __('dashboard.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox me-2"></i>
                                    {{ __('dashboard.no_records_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $unitsWithStatus->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    <!-- Edit Status Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>{{ __('dashboard.update_hk_status') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUnitId">
                    <div class="mb-3">
                        <label class="form-label">{{ __('dashboard.hk_status') }}</label>
                        <select id="editHkStatus" class="form-select">
                            <option value="clean">{{ __('dashboard.clean') }}</option>
                            <option value="dirty">{{ __('dashboard.dirty') }}</option>
                            <option value="inspected">{{ __('dashboard.inspected') }}</option>
                            <option value="out_of_service">{{ __('dashboard.out_of_service') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()">{{ __('dashboard.save') }}</button>
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

    <script>
        // Global function for iframe onload
        function iframeLoaded() {
            setTimeout(function() {
                switchPrintLang('en');
            }, 300);
        }
    </script>
@endsection

@push('scripts')
    <script>
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
        @if(request()->hasAny(['housekeeping_status', 'floor_id', 'unit_type_id', 'unit_number']))
            document.querySelector('.filter-form__container').style.display = 'block';
        @endif

        // Print Modal Functions
        function openPrintModal() {
            const printUrl = '{{ route("dashboard.housekeeping_status.print") }}?' + new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString();
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

        function printWithFilters() {
            openPrintModal();
        }

        function switchPrintLang(lang) {
            const iframe = document.getElementById('printIframe');
            try {
                if (iframe && iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                    iframe.contentWindow.switchLanguage(lang);
                } else {
                    setTimeout(function() {
                        if (iframe.contentWindow && typeof iframe.contentWindow.switchLanguage === 'function') {
                            iframe.contentWindow.switchLanguage(lang);
                        }
                    }, 500);
                }
            } catch(e) {
            }
        }

        function printPage() {
            const iframe = document.querySelector('#printIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        }

        const housekeepingStatusUpdateUrlTemplate = @json(route('housekeeping.updateStatus', ['unit' => '__UNIT__']));

        // Mark as Clean
        function markAsClean(unitId) {
            updateHousekeepingStatus(unitId, 'clean');
        }

        // Edit Status
        function editStatus(unitId, currentStatus) {
            document.getElementById('editUnitId').value = unitId;
            document.getElementById('editHkStatus').value = currentStatus;
            var modal = new bootstrap.Modal(document.getElementById('editStatusModal'));
            modal.show();
        }

        // Save Status
        function saveStatus() {
            const unitId = document.getElementById('editUnitId').value;
            const status = document.getElementById('editHkStatus').value;
            updateHousekeepingStatus(unitId, status);
        }

        // Update Housekeeping Status via AJAX
        function updateHousekeepingStatus(unitId, status) {
            const url = housekeepingStatusUpdateUrlTemplate.replace('__UNIT__', encodeURIComponent(unitId));

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to update housekeeping status (${response.status})`);
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update table row
                    const row = document.querySelector(`tr[data-unit-id="${unitId}"]`);
                    if (row) {
                        row.dataset.hkStatus = status;

                        // Update status badge
                        const statusCell = row.querySelector('td:nth-child(2)');
                        const statusLabels = {
                            'clean': '{{ __('dashboard.clean') }}',
                            'dirty': '{{ __('dashboard.dirty') }}',
                            'inspected': '{{ __('dashboard.inspected') }}',
                            'out_of_service': '{{ __('dashboard.out_of_service') }}'
                        };
                        const statusClasses = {
                            'clean': 'bg-success',
                            'dirty': 'bg-warning text-dark',
                            'inspected': 'bg-info',
                            'out_of_service': 'bg-secondary'
                        };
                        statusCell.innerHTML = `<span class="badge ${statusClasses[status]}">${statusLabels[status]}</span>`;
                    }

                    // Hide modal if open
                    var modalEl = document.getElementById('editStatusModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }

                    // Show success message
                    alert(data.message);

                    // Optionally reload page to update counts
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
@endpush
