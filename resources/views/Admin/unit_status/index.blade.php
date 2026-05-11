@extends('layouts.app')

@section('title', __('dashboard.units_status'))

@section('content')
    <main class="u-white-bg bg-white p-2" style="border-radius:10px;">

        <div class="unit-status-container"
            style="font-family: 'Inter', sans-serif; max-width: 1440px; margin: 0 auto; padding: 20px; background-color: #f8f9fc;">

            {{-- Page Header --}}
            <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 24px; font-weight: 600; color: #2c3e50; margin: 0;">{{ __('dashboard.units_status') }}</h2>
                <button type="button" id="toggleFilterBtn" class="btn btn-primary" onclick="toggleFilter()">
                    <i class="fas fa-filter me-2"></i>{{ __('dashboard.filter') }}
                </button>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('dashboard.unit_status.index') }}" id="filterForm">
                <div class="filter-form__container" style="background-color: white; border-radius: 12px; padding: 24px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">

                        {{-- Floor --}}
                        <div style="flex: 1 1 calc(25% - 15px); min-width: 200px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #5a6a7e; margin-bottom: 6px;">{{ __('dashboard.floor') }}</label>
                            <select name="floor_id" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background-color: white;">
                                <option value="">{{ __('dashboard.all_floors') }}</option>
                                @foreach($floors as $floor)
                                    <option value="{{ $floor->id }}" {{ request('floor_id') == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Unit No. --}}
                        <div style="flex: 1 1 calc(25% - 15px); min-width: 200px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #5a6a7e; margin-bottom: 6px;">{{ __('dashboard.unit_number') }}</label>
                            <input type="text" name="unit_number" value="{{ request('unit_number') }}" placeholder="{{ __('dashboard.enter_unit_number') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748;">
                        </div>

                        {{-- Unit Type --}}
                        <div style="flex: 1 1 calc(25% - 15px); min-width: 200px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #5a6a7e; margin-bottom: 6px;">{{ __('dashboard.unit_type') }}</label>
                            <select name="unit_type_id" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background-color: white;">
                                <option value="">{{ __('dashboard.all_types') }}</option>
                                @foreach($unitTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('unit_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Block --}}
                        <div style="flex: 1 1 calc(25% - 15px); min-width: 200px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #5a6a7e; margin-bottom: 6px;">{{ __('dashboard.block') }}</label>
                            <select name="block_id" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; color: #2d3748; background-color: white;">
                                <option value="">{{ __('dashboard.all_blocks') }}</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Search Button --}}
                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                        <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Status Tabs --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">

                {{-- Occupancy Tabs --}}
                <div style="display: flex; gap: 10px; background-color: white; border-radius: 8px; padding: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <button class="occupancy-tab active" data-status="all" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: #3498db; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        <i class="fas fa-home"></i> {{ __('dashboard.all') }} <span style="background-color: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 12px; margin-left: 5px;">{{ $unitsWithStatus->count() }}</span>
                    </button>
                    <button class="occupancy-tab" data-status="vacant" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-home"></i> {{ __('dashboard.vacant') }} <span style="background-color: #e2e8f0; padding: 2px 8px; border-radius: 12px; margin-left: 5px;">{{ $vacantCount }}</span>
                    </button>
                    <button class="occupancy-tab" data-status="occupied" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-home"></i> {{ __('dashboard.occupied') }} <span style="background-color: #e2e8f0; padding: 2px 8px; border-radius: 12px; margin-left: 5px;">{{ $occupiedCount }}</span>
                    </button>
                </div>

                {{-- Check In/Out Tabs --}}
                <div style="display: flex; gap: 10px; background-color: white; border-radius: 8px; padding: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <button style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: default;">
                        <i class="fas fa-sign-in-alt"></i> {{ __('dashboard.check_in_today') }} <span style="background-color: #e2e8f0; padding: 2px 8px; border-radius: 12px; margin-left: 5px;">{{ $checkInToday }}</span>
                    </button>
                    <button style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: default;">
                        <i class="fas fa-sign-out-alt"></i> {{ __('dashboard.check_out_today') }} <span style="background-color: #e2e8f0; padding: 2px 8px; border-radius: 12px; margin-left: 5px;">{{ $checkOutToday }}</span>
                    </button>
                </div>
            </div>

            {{-- Housekeeping Status Tabs --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; gap: 10px; background-color: white; border-radius: 8px; padding: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <button class="housekeeping-tab active" data-hk="all" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                        <i class="fas fa-broom"></i> {{ __('dashboard.all') }}
                    </button>
                    <button class="housekeeping-tab" data-hk="clean" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i> {{ __('dashboard.clean') }}
                    </button>
                    <button class="housekeeping-tab" data-hk="dirty" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-broom" style="color: #f59e0b;"></i> {{ __('dashboard.dirty') }}
                    </button>
                    <button class="housekeeping-tab" data-hk="inspected" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-clipboard-check" style="color: #3b82f6;"></i> {{ __('dashboard.inspected') }}
                    </button>
                    <button class="housekeeping-tab" data-hk="out_of_service" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background-color: transparent; color: #5a6a7e; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-tools" style="color: #ef4444;"></i> {{ __('dashboard.out_of_service') }}
                    </button>
                </div>
            </div>

            {{-- Units Grid --}}
            <div id="unitsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @forelse($unitsWithStatus as $unit)
                    <div class="unit-card" data-status="{{ $unit->is_occupied ? 'occupied' : 'vacant' }}" data-hk-status="{{ $unit->housekeeping_status ?? 'clean' }}"
                        style="background-color: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); position: relative; border: 1px solid #edf2f7;">

                        {{-- Header --}}
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <div>
                                @if(!$unit->is_occupied)
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ __('dashboard.vacant') }}</span>
                                @else
                                    <span style="background-color: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">{{ __('dashboard.occupied') }}</span>
                                @endif
                            </div>
                            <div>
                                <i class="fas fa-home" style="color: {{ $unit->is_occupied ? '#ed8936' : '#48bb78' }};"></i>
                            </div>
                        </div>

                        {{-- Unit Info --}}
                        <div style="display: flex; gap: 12px;">
                            <div style="position: relative;">
                                <div style="width: 48px; height: 48px; background-color: {{ $unit->is_occupied ? '#fffaf0' : '#f0fff4' }}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-home" style="color: {{ $unit->is_occupied ? '#ed8936' : '#48bb78' }}; font-size: 20px;"></i>
                                </div>
                            </div>
                            <div>
                                <h3 style="font-size: 20px; font-weight: 700; color: #2d3748; margin: 0 0 4px 0;">{{ $unit->unit_number }}</h3>
                                <p style="font-size: 13px; color: #718096; margin: 0 0 4px 0;">{{ $unit->unitType->name ?? '-' }}</p>

                                @if($unit->is_occupied && $unit->current_guest)
                                    <p style="font-size: 14px; font-weight: 500; color: #2d3748; margin: 0 0 4px 0;">{{ $unit->current_guest }}</p>
                                    @if($unit->balance > 0)
                                        <p style="font-size: 14px; font-weight: 600; color: #e53e3e; margin: 0;">{{ number_format($unit->balance, 2) }}</p>
                                    @endif
                                @else
                                    <p style="font-size: 14px; font-weight: 500; color: #718096; margin: 0;">—</p>
                                @endif
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                            <div style="display: flex; gap: 8px;">
                                @php
                                    $hkStatus = $unit->housekeeping_status ?? 'clean';
                                    $hkColors = [
                                        'clean' => '#10b981',
                                        'dirty' => '#f59e0b',
                                        'inspected' => '#3b82f6',
                                        'out_of_service' => '#ef4444'
                                    ];
                                    $hkIcons = [
                                        'clean' => 'fa-check',
                                        'dirty' => 'fa-broom',
                                        'inspected' => 'fa-clipboard-check',
                                        'out_of_service' => 'fa-tools'
                                    ];
                                    $hkColor = $hkColors[$hkStatus] ?? '#10b981';
                                    $hkIcon = $hkIcons[$hkStatus] ?? 'fa-check';
                                @endphp
                                <button type="button"
                                    class="status-update-btn"
                                    data-unit-id="{{ $unit->id }}"
                                    data-unit-number="{{ $unit->unit_number }}"
                                    data-current-status="{{ $hkStatus }}"
                                    style="width: 32px; height: 32px; background-color: {{ $hkColor }}; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s;"
                                    title="{{ __( 'dashboard.' . $hkStatus ) }}">
                                    <i class="fas {{ $hkIcon }}" style="color: white; font-size: 14px;"></i>
                                </button>
                            </div>
                            <a href="javascript:void(0)" class="unit-detail-btn"
                               data-unit-id="{{ $unit->id }}"
                               data-unit-number="{{ $unit->unit_number }}"
                               data-unit-type="{{ $unit->unitType->name ?? '-' }}"
                               data-floor="{{ $unit->floor->name ?? '-' }}"
                               data-block="{{ $unit->block->name ?? '-' }}"
                               data-status="{{ $unit->is_occupied ? 'Occupied' : 'Vacant' }}"
                               data-guest="{{ $unit->current_guest ?? '-' }}"
                               data-balance="{{ $unit->balance ?? 0 }}"
                               style="padding: 8px 16px; background-color: transparent; border: 1px solid #e2e8f0; border-radius: 6px; color: #4a5568; font-size: 13px; font-weight: 500; text-decoration: none; cursor: pointer;">
                                {{ __('dashboard.unit_details') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #718096;">
                        <i class="fas fa-home" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p>{{ __('dashboard.no_units_found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Unit Details Modal -->
    <div class="modal fade" id="unitDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-door-open me-2"></i>{{ __('dashboard.unit_details') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary" id="modalUnitNumber">-</h3>
                        <span class="badge" id="modalStatusBadge">-</span>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">{{ __('dashboard.unit_type') }}</small>
                                <strong id="modalUnitType">-</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">{{ __('dashboard.floor') }}</small>
                                <strong id="modalFloor">-</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">{{ __('dashboard.block') }}</small>
                                <strong id="modalBlock">-</strong>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">{{ __('dashboard.guest') }}</small>
                                <strong id="modalGuest">-</strong>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">{{ __('dashboard.balance') }}</small>
                                <strong id="modalBalance" class="text-danger">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-broom me-2"></i>{{ __('dashboard.update_status') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary" id="statusModalUnitNumber">Unit -</h3>
                        <small class="text-muted" id="statusModalCurrentStatus">{{ __('dashboard.current_status') }}: -</small>
                    </div>

                    <form id="statusUpdateForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('dashboard.select_status') }}</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="status-option-btn" data-status="clean" style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; text-align: left; transition: all 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="width: 32px; height: 32px; background-color: #10b981; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-check" style="color: white;"></i>
                                        </span>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 600; color: #2d3748;">{{ __('dashboard.clean') }}</div>
                                            <small style="color: #718096;">{{ __('dashboard.unit_is_clean') }}</small>
                                        </div>
                                    </div>
                                </button>

                                <button type="button" class="status-option-btn" data-status="dirty" style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; text-align: left; transition: all 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="width: 32px; height: 32px; background-color: #f59e0b; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-broom" style="color: white;"></i>
                                        </span>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 600; color: #2d3748;">{{ __('dashboard.dirty') }}</div>
                                            <small style="color: #718096;">{{ __('dashboard.unit_needs_cleaning') }}</small>
                                        </div>
                                    </div>
                                </button>

                                <button type="button" class="status-option-btn" data-status="inspected" style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; text-align: left; transition: all 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="width: 32px; height: 32px; background-color: #3b82f6; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-clipboard-check" style="color: white;"></i>
                                        </span>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 600; color: #2d3748;">{{ __('dashboard.inspected') }}</div>
                                            <small style="color: #718096;">{{ __('dashboard.unit_is_inspected') }}</small>
                                        </div>
                                    </div>
                                </button>

                                <button type="button" class="status-option-btn" data-status="out_of_service" style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; text-align: left; transition: all 0.2s;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="width: 32px; height: 32px; background-color: #ef4444; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-tools" style="color: white;"></i>
                                        </span>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 600; color: #2d3748;">{{ __('dashboard.out_of_service') }}</div>
                                            <small style="color: #718096;">{{ __('dashboard.unit_out_of_service') }}</small>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <input type="hidden" id="selectedStatus" name="housekeeping_status" value="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn" disabled>
                        <i class="fas fa-save me-2"></i>{{ __('dashboard.update') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .occupancy-tab {
        cursor: pointer;
        transition: all 0.2s;
    }
    .occupancy-tab.active {
        background-color: #3498db !important;
        color: white !important;
    }
    .unit-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .unit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .unit-card.hidden {
        display: none;
    }
    .housekeeping-tab {
        cursor: pointer;
        transition: all 0.2s;
    }
    .housekeeping-tab.active {
        background-color: #6c757d !important;
        color: white !important;
    }
    .status-update-btn {
        transition: all 0.2s !important;
    }
    .status-update-btn:hover {
        transform: scale(1.1) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    }
    .status-option-btn {
        transition: all 0.2s !important;
    }
    .status-option-btn:hover {
        border-color: #cbd5e0 !important;
        background-color: #f7fafc !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleFilter() {
        var filterForm = document.getElementById('filterForm');
        var currentDisplay = filterForm.style.display;
        if (currentDisplay === 'none' || currentDisplay === '') {
            filterForm.style.display = 'block';
        } else {
            filterForm.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Hide filter form by default
        document.getElementById('filterForm').style.display = 'none';

        // Current filter state
        var currentOccupancyStatus = 'all';
        var currentHkStatus = 'all';

        // Function to apply both filters
        function applyFilters() {
            document.querySelectorAll('.unit-card').forEach(function(card) {
                var matchesOccupancy = currentOccupancyStatus === 'all' || card.dataset.status === currentOccupancyStatus;
                var matchesHk = currentHkStatus === 'all' || card.dataset.hkStatus === currentHkStatus;

                if (matchesOccupancy && matchesHk) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Occupancy tab filtering
        document.querySelectorAll('.occupancy-tab').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.occupancy-tab').forEach(function(b) {
                    b.classList.remove('active');
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#5a6a7e';
                });
                btn.classList.add('active');
                btn.style.backgroundColor = '#3498db';
                btn.style.color = 'white';

                // Update current occupancy status and apply filters
                currentOccupancyStatus = btn.dataset.status;
                applyFilters();
            });
        });

        // Housekeeping tab filtering
        document.querySelectorAll('.housekeeping-tab').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.housekeeping-tab').forEach(function(b) {
                    b.classList.remove('active');
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#5a6a7e';
                });
                btn.classList.add('active');
                btn.style.backgroundColor = '#6c757d';
                btn.style.color = 'white';

                // Update current housekeeping status and apply filters
                currentHkStatus = btn.dataset.hk;
                applyFilters();
            });
        });

        // Unit detail modal
        document.querySelectorAll('.unit-detail-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var unitNumber = this.dataset.unitNumber;
                var unitType = this.dataset.unitType;
                var floor = this.dataset.floor;
                var block = this.dataset.block;
                var status = this.dataset.status;
                var guest = this.dataset.guest;
                var balance = this.dataset.balance;

                document.getElementById('modalUnitNumber').textContent = unitNumber;
                document.getElementById('modalUnitType').textContent = unitType;
                document.getElementById('modalFloor').textContent = floor;
                document.getElementById('modalBlock').textContent = block;
                document.getElementById('modalGuest').textContent = guest;
                document.getElementById('modalBalance').textContent = balance > 0 ? balance + ' SAR' : '0';

                var statusBadge = document.getElementById('modalStatusBadge');
                if (status === 'Occupied') {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = '{{ __('dashboard.occupied') }}';
                } else {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = '{{ __('dashboard.vacant') }}';
                }

                var modal = new bootstrap.Modal(document.getElementById('unitDetailModal'));
                modal.show();
            });
        });

        // Status update modal functionality
        var currentUnitId = null;
        var statusUpdateModal = null;

        // Status translation mapping
        var statusLabels = {
            'clean': '{{ __('dashboard.clean') }}',
            'dirty': '{{ __('dashboard.dirty') }}',
            'inspected': '{{ __('dashboard.inspected') }}',
            'out_of_service': '{{ __('dashboard.out_of_service') }}'
        };

        // Open status update modal
        document.querySelectorAll('.status-update-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                currentUnitId = this.dataset.unitId;
                var unitNumber = this.dataset.unitNumber;
                var currentStatus = this.dataset.currentStatus;

                // Update modal header
                document.getElementById('statusModalUnitNumber').textContent = unitNumber;
                document.getElementById('statusModalCurrentStatus').textContent = '{{ __('dashboard.current_status') }}: ' + statusLabels[currentStatus];

                // Reset selections
                document.getElementById('selectedStatus').value = '';
                document.getElementById('updateStatusBtn').disabled = true;
                document.querySelectorAll('.status-option-btn').forEach(function(option) {
                    option.style.borderColor = '#e2e8f0';
                    option.style.backgroundColor = 'white';
                });

                // Show modal
                statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
                statusUpdateModal.show();
            });
        });

        // Status option selection
        document.querySelectorAll('.status-option-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove previous selection styling
                document.querySelectorAll('.status-option-btn').forEach(function(option) {
                    option.style.borderColor = '#e2e8f0';
                    option.style.backgroundColor = 'white';
                });

                // Add selection styling to clicked button
                this.style.borderColor = '#3498db';
                this.style.backgroundColor = '#f0f9ff';

                // Store selected status
                var selectedStatus = this.dataset.status;
                document.getElementById('selectedStatus').value = selectedStatus;
                document.getElementById('updateStatusBtn').disabled = false;
            });
        });

        // Update status button click
        document.getElementById('updateStatusBtn').addEventListener('click', function() {
            var selectedStatus = document.getElementById('selectedStatus').value;

            if (!selectedStatus || !currentUnitId) {
                alert('{{ __('messages.please_select_status') }}');
                return;
            }

            // Disable button during request
            this.disabled = true;
            var btn = this;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('dashboard.updating') }}...';

            var updateStatusUrl = @json(route('dashboard.unit_status.update', ['unit' => '__UNIT_ID__']))
                .replace('__UNIT_ID__', currentUnitId);

            // Make API request
            fetch(updateStatusUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    housekeeping_status: selectedStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Status update failed with HTTP ' + response.status);
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert(data.message);

                    // Close modal
                    statusUpdateModal.hide();

                    // Reload page to refresh status
                    location.reload();
                } else {
                    alert(data.message || '{{ __('messages.error_updating_unit_status') }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('messages.error_updating_unit_status') }}');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    });
</script>
@endpush
