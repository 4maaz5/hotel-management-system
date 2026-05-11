@extends('layouts.app')

@section('title', 'Reservation')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
@endpush

@section('content')
    @php
        $defaultReservationView = ($settings->default_view ?? 'list') === 'calendar' ? 'calendar' : 'list';
    @endphp
    <main class="u-white-bg bg-white p-2" style="border-radius:10px;">

        <div class="container-fluid mt-4">

            <!-- PAGE HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">{{ __('dashboard.reservations') }}</h3>

                <div class="d-flex gap-2 flex-wrap">
                    <!-- View Toggle -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary {{ $defaultReservationView === 'list' ? 'active' : '' }}" id="listViewBtn" onclick="switchView('list')">
                            <i class="bi bi-list-ul"></i> {{ __('dashboard.calendar_list_view') }}
                        </button>
                        <button type="button" class="btn btn-outline-primary {{ $defaultReservationView === 'calendar' ? 'active' : '' }}" id="calendarViewBtn" onclick="switchView('calendar')">
                            <i class="bi bi-calendar3"></i> {{ __('dashboard.calendar_calendar_view') }}
                        </button>
                    </div>

                    @can('reservation.contract')
                        <a href="{{ route('dashboard.reservation.contract_template') }}" class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-printer"></i> {{ __('dashboard.contract_template') }}
                        </a>
                    @endcan


                    {{-- <button class="btn btn-outline-secondary">
                        {{ __('dashboard.guest_portal_links') }}
                    </button> --}}

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitAvailabilityModal">
                        {{ __('dashboard.unit_availability') }}
                    </button>

                    <!-- Spacer to push filter button to right -->
                    <div class="ms-auto"></div>

                    <button class="btn btn-primary n-button n-button--primary">
                        <i class="bi bi-funnel"></i> {{ __('dashboard.filter') }}
                    </button>
                    @can('reservation.add')
                        <a href="{{ route('dashboard.reservation.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> {{ __('dashboard.new_reservation') }}
                        </a>
                    @endcan

                </div>
            </div>



            <!-- Filter Form -->
            <form method="GET" action="{{ route('dashboard.reservation.index') }}">
                <div class="filter-form__container mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">

                                <!-- Name Filter -->
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">{{ __('dashboard.guest_name') }}</label>
                                    <input type="text" name="guest_name" value="{{ request('guest_name') }}"
                                        class="form-control" placeholder="{{ __('dashboard.search_by_guest_name') }}">
                                </div>

                                <!-- Unit Filter -->
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">{{ __('dashboard.unit') }}</label>
                                    <input type="text" name="unit_number" value="{{ request('unit_number') }}"
                                        class="form-control" placeholder="{{ __('dashboard.unit_number') }}">
                                </div>

                                <!-- Status Filter -->
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">{{ __('dashboard.status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('dashboard.all') }}</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                            {{ __('dashboard.pending') }}
                                        </option>
                                        <option value="confirmed"
                                            {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                                            {{ __('dashboard.confirmed') }}
                                        </option>
                                        <option value="checked_in"
                                            {{ request('status') === 'checked_in' ? 'selected' : '' }}>
                                            {{ __('dashboard.checked_in') }}
                                        </option>
                                        <option value="checked_out"
                                            {{ request('status') === 'checked_out' ? 'selected' : '' }}>
                                            {{ __('dashboard.checked_out') }}
                                        </option>
                                        <option value="cancelled"
                                            {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                            {{ __('dashboard.cancelled') }}
                                        </option>
                                        <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>
                                            {{ __('dashboard.no_show') }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Check-in Date Filter -->
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">{{ __('dashboard.check_in') }}</label>
                                    <input type="date" name="check_in_from" value="{{ request('check_in_from') }}"
                                        class="form-control">
                                </div>

                                <!-- Check-out Date Filter -->
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">{{ __('dashboard.check_out') }}</label>
                                    <input type="date" name="check_out_from" value="{{ request('check_out_from') }}"
                                        class="form-control">
                                </div>

                                <!-- Buttons -->
                                <div class="col-lg-1 col-md-4 d-flex align-items-end">
                                    <button type="submit"
                                        class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>


            <!-- RESERVATION GRID -->
            <div class="card shadow-sm {{ $defaultReservationView === 'calendar' ? 'd-none' : '' }}">
                <div class="table-responsive" id="reservationTableWrap">
                    <table class="table table-hover align-middle text-center mb-0">

                        <thead class="table-light">
                            <tr>
                                <th style="white-space: nowrap;"></th>
                                <th style="white-space: nowrap;">{{ __('dashboard.registration_number') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.status') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.guest') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.unit') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.check_in') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.check_out') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.nights') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.total_rent') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.amount') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.taxes') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.total') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.deposit') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.paid') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.balance') }}</th>
                                <th style="white-space: nowrap;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($reservations as $reservation)
                                <tr>
                                    <td><i class="bi bi-person-circle fs-5 text-secondary"></i></td>
                                    <td>
                                        <a href="{{ route('dashboard.reservation.edit', $reservation->id) }}"
                                            class="fw-bold text-primary">{{ $reservation->reservation_number }}</a>
                                    </td>
                                    <td>
                                        @switch($reservation->status)
                                            @case('pending')
                                                <span class="badge bg-warning">@lang('dashboard.pending')</span>
                                            @break

                                            @case('confirmed')
                                                <span class="badge bg-success">@lang('dashboard.confirmed')</span>
                                            @break

                                            @case('checked_in')
                                                <span class="badge bg-primary">@lang('dashboard.checked_in')</span>
                                            @break

                                            @case('checked_out')
                                                <span class="badge bg-secondary">@lang('dashboard.checked_out')</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">@lang('dashboard.cancelled')</span>
                                            @break

                                            @case('no_show')
                                                <span class="badge bg-dark">@lang('dashboard.no_show')</span>
                                            @break

                                            @default
                                                <span class="badge bg-info">{{ $reservation->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($reservation->guest)
                                            {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                                        @elseif($reservation->corporate)
                                            {{ $reservation->corporate->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $reservation->unit->unit_number ?? '-' }}</div>
                                        <small class="text-muted">{{ $reservation->unit->unitType->name ?? '' }}</small>
                                    </td>
                                    <td>{{ $reservation->check_in_date->format('Y/m/d') }}</td>
                                    <td>{{ $reservation->check_out_date->format('Y/m/d') }}</td>
                                    <td>{{ $reservation->nights }}</td>
                                    <td>{{ number_format($reservation->total_rent, 2) }}</td>
                                    <td>{{ number_format($reservation->subtotal, 2) }}</td>
                                    <td>{{ number_format($reservation->total_taxes_fees, 2) }}</td>
                                    <td>{{ number_format($reservation->grand_total, 2) }}</td>
                                    <td>{{ number_format($reservation->security_deposit, 2) }}</td>
                                    <td>{{ number_format($reservation->paid_amount, 2) }}</td>
                                    <td class="{{ $reservation->balance > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                        {{ number_format($reservation->balance, 2) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @can('reservation.edit')
                                                <a href="{{ route('dashboard.reservation.edit', $reservation->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan

                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#cancelReservationModal"
                                                            data-reservation-id="{{ $reservation->id }}"
                                                            data-total-rent="{{ $reservation->total_rent }}">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ __('dashboard.cancel_reservation') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                           onclick="openContractModal('{{ route('dashboard.reservation.contract_modal', $reservation->id) }}')">
                                                            <i class="bi bi-file-text"></i>
                                                            {{ __('dashboard.reservation_contract') }}
                                                        </a>
                                                    </li>


                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center py-4">
                                            {{ __('dashboard.no_reservations_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>

                <!-- Calendar View Container -->
                <div id="calendarContainer" class="{{ $defaultReservationView === 'calendar' ? '' : 'd-none' }}">
                <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden;">
                    <div class="card-header border-0 d-flex flex-wrap align-items-center gap-2" style="background: #fff; padding: 14px 20px; border-bottom: 1px solid #e2e8f0 !important;">
                        <span class="calendar-legend-badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">
                            <i class="fas fa-circle me-1" style="font-size: 6px; color: #f59e0b;"></i>{{ __('dashboard.calendar_pending') }}
                        </span>
                        <span class="calendar-legend-badge" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
                            <i class="fas fa-circle me-1" style="font-size: 6px; color: #3b82f6;"></i>{{ __('dashboard.calendar_confirmed') }}
                        </span>
                        <span class="calendar-legend-badge" style="background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7;">
                            <i class="fas fa-circle me-1" style="font-size: 6px; color: #10b981;"></i>{{ __('dashboard.calendar_checked_in') }}
                        </span>
                        <span class="calendar-legend-badge" style="background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1;">
                            <i class="fas fa-circle me-1" style="font-size: 6px; color: #64748b;"></i>{{ __('dashboard.calendar_checked_out') }}
                        </span>
                        <span class="calendar-legend-badge" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                            <i class="fas fa-circle me-1" style="font-size: 6px; color: #ef4444;"></i>{{ __('dashboard.calendar_cancelled') }}
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        </main>
        <div class="modal fade" id="cancelReservationModal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <form method="POST" action="{{ route('dashboard.reservation.cancel') }}">
                    @csrf

                    <input type="hidden" name="reservation_id" id="reservation_id">

                    <div class="modal-content shadow-sm">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-x-circle text-danger me-2"></i>
                                {{ __('dashboard.cancel_reservation') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Cancel Reason -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.cancellation_reason') }}
                                </label>

                                <select id="cancel_reason_id" name="cancel_reason_id" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.select_reason') }}
                                    </option>

                                    @foreach ($cancelReasons as $reason)
                                        <option value="{{ $reason->id }}">
                                            {{ $reason->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <!-- Penalty -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.applicable_penalty') }}
                                </label>

                                <select id="penalty_id" name="penalty_id" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.select_penalty') }}
                                    </option>
                                </select>
                            </div>


                            <!-- Penalty Amount -->
                            <div class="border rounded p-3 bg-light mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">
                                        {{ __('dashboard.penalty_amount') }}
                                    </span>

                                    <span class="text-danger fw-bold fs-5" id="penalty_amount">
                                        0
                                    </span>
                                </div>
                            </div>

                            <!-- Refund Amount -->
                            <div class="border rounded p-3 bg-success bg-opacity-10">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">
                                        {{ __('dashboard.refund_amount') }}
                                    </span>

                                    <span class="text-success fw-bold fs-5" id="refund_amount">
                                        0
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="refund_amount" id="refund_amount_input">

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                                {{ __('dashboard.close') }}
                            </button>

                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ __('dashboard.confirm_cancellation') }}
                            </button>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endsection
    @push('styles')
    <style>
        .fc .fc-toolbar-title {
            font-size: 1.2rem !important;
            font-weight: 600;
        }

        .fc .fc-button {
            background-color: #4a6cf7 !important;
            border-color: #4a6cf7 !important;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .fc .fc-button:hover {
            background-color: #3b5ce4 !important;
            border-color: #3b5ce4 !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #2b45c9 !important;
            border-color: #2b45c9 !important;
        }

        .fc .fc-daygrid-day-number {
            font-size: 0.9rem;
            color: #333;
            text-decoration: none;
        }

        .fc .fc-col-header-cell-cushion {
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
        }

        .fc-event {
            border: none !important;
            border-radius: 4px !important;
            padding: 2px 6px !important;
            font-size: 0.75rem !important;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .fc-event-status-pending {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .fc-event-status-confirmed {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .fc-event-status-checked_in {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .fc-event-status-checked_out {
            background-color: #6c757d !important;
            color: #fff !important;
        }

        .fc-event-status-no_show, .fc-event-status-cancelled {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(74, 108, 247, 0.1) !important;
        }

        .fc .fc-daygrid-day:hover {
            background-color: #f8f9fa;
        }

        .calendar-legend-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .calendar-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
        }

        .calendar-legend-color {
            width: 14px;
            height: 14px;
            border-radius: 3px;
        }

        .calendar-loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        #calendar {
            padding: 10px;
        }
    </style>
    @endpush

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            let calendar;
            const initialReservationView = @json($defaultReservationView);

            function switchView(view) {
                const listViewBtn = document.getElementById('listViewBtn');
                const calendarViewBtn = document.getElementById('calendarViewBtn');
                const tableContainer = document.getElementById('reservationTableWrap');
                const calendarContainer = document.getElementById('calendarContainer');

                if (view === 'calendar') {
                    listViewBtn.classList.remove('active');
                    calendarViewBtn.classList.add('active');
                    tableContainer.closest('.card').classList.add('d-none');
                    calendarContainer.classList.remove('d-none');

                    if (!calendar) {
                        initCalendar();
                    }
                    calendar.render();
                } else {
                    calendarViewBtn.classList.remove('active');
                    listViewBtn.classList.add('active');
                    calendarContainer.classList.add('d-none');
                    tableContainer.closest('.card').classList.remove('d-none');
                }
            }

            function initCalendar() {
                const calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: new Date(),
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listMonth'
                    },
                    events: '{{ route("dashboard.reservation.calendar_events") }}',
                    eventClick: function(info) {
                        showReservationModal(info.event);
                    },
                    eventDidMount: function(info) {
                        info.el.classList.add('fc-event-status-' + info.event.extendedProps.status);
                        info.el.setAttribute('data-bs-toggle', 'tooltip');
                        info.el.setAttribute('data-bs-placement', 'top');
                        info.el.title = info.event.extendedProps.guest_name + '\n' +
                                       'Unit: ' + info.event.extendedProps.unit_number + '\n' +
                                       'Status: ' + info.event.extendedProps.status + '\n' +
                                       'Nights: ' + info.event.extendedProps.nights;
                    },
                    height: 'auto',
                    dayMaxEvents: 3,
                    moreLinkClick: 'popover',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: 'short'
                    }
                });
            }

            function showReservationModal(event) {
                const props = event.extendedProps;
                let statusBadge = '';
                const statusColors = {
                    'pending': 'bg-warning',
                    'confirmed': 'bg-primary',
                    'checked_in': 'bg-success',
                    'checked_out': 'bg-secondary',
                    'no_show': 'bg-danger',
                    'cancelled': 'bg-danger'
                };
                statusBadge = '<span class="badge ' + (statusColors[props.status] || 'bg-secondary') + '">' + props.status.toUpperCase() + '</span>';

                const modalHtml = `
                    <div class="modal fade" id="calendarEventModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #4a6cf7 0%, #2b45c9 100%); color: white;">
                                    <h5 class="modal-title">
                                        <i class="bi bi-calendar-check me-2"></i>{{ __('dashboard.calendar_reservation_details') }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4">
                                        <h4 class="fw-bold text-primary">${props.unit_number}</h4>
                                        <p class="text-muted mb-0">${props.guest_name}</p>
                                        ${statusBadge}
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block">{{ __('dashboard.calendar_reservation') }}</small>
                                                <strong>${props.reservation_number}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block">{{ __('dashboard.calendar_nights') }}</small>
                                                <strong>${props.nights}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block">{{ __('dashboard.calendar_checkin') }}</small>
                                                <strong>${event.startStr}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block">{{ __('dashboard.calendar_checkout') }}</small>
                                                <strong>${event.endStr ? event.endStr : '-'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.calendar_close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                document.getElementById('calendarEventModal')?.remove();

                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('calendarEventModal'));
                modal.show();

                // Remove modal from DOM after hidden
                document.getElementById('calendarEventModal').addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            }

            const toggleBtn = document.querySelector('.n-button.n-button--primary');
            const filterContainer = document.querySelector('.filter-form__container');

            filterContainer.style.display = 'none';

            toggleBtn.addEventListener('click', function() {
                if (filterContainer.style.display === 'none') {
                    filterContainer.style.display = 'block';
                } else {
                    filterContainer.style.display = 'none';
                }
            });

            document.addEventListener("DOMContentLoaded", function() {
                switchView(initialReservationView);

                const cancelButtons = document.querySelectorAll('[data-reservation-id]');
                let currentTotalRent = 0;

                cancelButtons.forEach(button => {

                    button.addEventListener('click', function() {

                        const reservationId = this.getAttribute('data-reservation-id');
                        currentTotalRent = parseFloat(this.getAttribute('data-total-rent')) || 0;

                        document.getElementById('reservation_id').value = reservationId;
                        document.getElementById('refund_amount').setAttribute('data-original', currentTotalRent);
                        document.getElementById('refund_amount').textContent = currentTotalRent.toFixed(2);
                        document.getElementById('refund_amount_input').value = currentTotalRent.toFixed(2);
                        document.getElementById('penalty_amount').textContent = '0';

                    });

                });

            });


            const cancelReasonPenaltiesBaseUrl = @json(url('/app/cancel-reason'));

            $('#cancel_reason_id').change(function() {

                let reasonId = $(this).val();

                if (!reasonId) {
                    $('#penalty_id').html('<option value="">Select Penalty</option>');
                    $('#penalty_amount').text('0');
                    return;
                }

                $('#penalty_id').html('<option>Loading...</option>');

                fetch(`${cancelReasonPenaltiesBaseUrl}/${reasonId}/penalties`)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`Failed to load penalties (${res.status})`);
                        }

                        return res.json();
                    })
                    .then(data => {

                        let html = '<option value="">Select Penalty</option>';
                        let autoAppliedPenalty = null;

                        data.forEach(p => {

                            html += `<option value="${p.id}" data-value="${p.value}">
                        ${p.name} (${p.value})
                    </option>`;

                            if (p.pivot && p.pivot.auto_apply == 1) {
                                autoAppliedPenalty = p;
                            }

                        });

                        $('#penalty_id').html(html);

                        if (autoAppliedPenalty) {
                            $('#penalty_id').val(autoAppliedPenalty.id);
                            $('#penalty_amount').text(autoAppliedPenalty.value);
                        } else {
                            $('#penalty_amount').text('0');
                        }

                    })
                    .catch(error => {
                        console.error('Failed to load penalties for cancel reason:', error);
                        $('#penalty_id').html('<option value="">Select Penalty</option>');
                        $('#penalty_amount').text('0');
                    });

            });

            $('#penalty_id').change(function() {

                let value = parseFloat($(this).find(':selected').data('value')) || 0;

                $('#penalty_amount').text(value.toFixed(2));

                let totalRent = parseFloat($('#refund_amount').attr('data-original')) || 0;
                if (totalRent === 0) {
                    totalRent = parseFloat($('#refund_amount').textContent) || 0;
                }
                let refund = Math.max(0, totalRent - value);
                $('#refund_amount').text(refund.toFixed(2));
                $('#refund_amount_input').val(refund.toFixed(2));

            });

            // Contract Modal Functions
            function openContractModal(url) {
                var reservationId = url.split('/').filter(function(part) { return part.match(/^[0-9]+$/); })[0];
                $('#contractModal iframe').attr('src', url);
                $('#downloadPdfBtn').attr('href', '/dashboard/reservation/' + reservationId + '/contract');
                $('#contractModal').modal('show');
            }

            function switchContractLang(lang) {
                const iframe = document.querySelector('#contractModal iframe');
                if (iframe && iframe.contentWindow && iframe.contentWindow.switchLanguage) {
                    iframe.contentWindow.switchLanguage(lang);
                }
            }

            function printContract() {
                const iframe = document.querySelector('#contractModal iframe');
                if (iframe && iframe.contentWindow && iframe.contentWindow.printContract) {
                    iframe.contentWindow.printContract();
                }
            }

            function downloadContractPdf() {
                const iframe = document.querySelector('#contractModal iframe');
                if (iframe && iframe.contentWindow) {
                    const lang = iframe.contentWindow.currentLang || 'en';
                    window.location.href = '{{ route("dashboard.reservation.contract", ":id") }}'.replace(':id', '{{ $reservation->id ?? "" }}') + '?lang=' + lang;
                }
            }
        </script>
    @endpush

    <!-- Contract Modal -->
    <div class="modal fade" id="contractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="switchContractLang('en')">
                            English
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchContractLang('ar')">
                            العربية
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchContractLang('both')">
                            Both
                        </button>
                    </div>
                    <div class="btn-group ms-3" role="group">
                        <button type="button" class="btn btn-primary" onclick="printContract()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="contractIframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Availability Modal -->
    <div class="modal fade" id="unitAvailabilityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.unit_availability') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.search') }}</label>
                            <input type="text" class="form-control" id="availabilitySearch" placeholder="Search unit...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.check_in_date') }}</label>
                            <input type="date" class="form-control" id="availabilityCheckIn">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.check_out_date') }}</label>
                            <input type="date" class="form-control" id="availabilityCheckOut">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.unit_type') }}</label>
                            <select class="form-select" id="availabilityUnitType">
                                <option value="">All Unit Types</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="searchAvailableUnits()">
                            <i class="bi bi-search"></i> {{ __('dashboard.search') }}
                        </button>
                    </div>
                    <div id="availableUnitsList" class="row">
                        <p class="text-muted text-center">Loading units...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load units when modal opens
        document.getElementById('unitAvailabilityModal').addEventListener('shown.bs.modal', function () {
            loadAllUnits();
        });

        function loadAllUnits() {
            const listDiv = document.getElementById('availableUnitsList');
            const search = document.getElementById('availabilitySearch').value;
            const unitTypeId = document.getElementById('availabilityUnitType').value;

            listDiv.innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';

            fetch('{{ route("dashboard.reservation.available_units") }}?search=' + search + '&unit_type_id=' + unitTypeId)
                .then(response => response.json())
                .then(data => {
                    renderUnits(data);
                })
                .catch(error => {
                    listDiv.innerHTML = '<p class="text-danger text-center">Error loading units</p>';
                    console.error(error);
                });
        }

        function searchAvailableUnits() {
            const checkIn = document.getElementById('availabilityCheckIn').value;
            const checkOut = document.getElementById('availabilityCheckOut').value;
            const unitTypeId = document.getElementById('availabilityUnitType').value;
            const search = document.getElementById('availabilitySearch').value;

            if (checkIn && checkOut && checkIn >= checkOut) {
                alert('Check-out date must be after check-in date');
                return;
            }

            const listDiv = document.getElementById('availableUnitsList');
            listDiv.innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';

            let url = '{{ route("dashboard.reservation.available_units") }}?';
            if (checkIn && checkOut) {
                url += 'check_in_date=' + checkIn + '&check_out_date=' + checkOut + '&';
            }
            url += 'unit_type_id=' + unitTypeId + '&search=' + search;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    renderUnits(data);
                })
                .catch(error => {
                    listDiv.innerHTML = '<p class="text-danger text-center">Error loading units</p>';
                    console.error(error);
                });
        }

        function renderUnits(data) {
            const listDiv = document.getElementById('availableUnitsList');

            // Populate unit types
            const unitTypeSelect = document.getElementById('availabilityUnitType');
            if (unitTypeSelect.options.length <= 1 && data.unit_types.length > 0) {
                data.unit_types.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.name;
                    unitTypeSelect.appendChild(option);
                });
            }

            if (data.units.length === 0) {
                listDiv.innerHTML = '<p class="text-danger text-center">No units found</p>';
                return;
            }

            let html = '';
            data.units.forEach(unit => {
                html += '<div class="col-md-3 col-sm-6 mb-3">';
                html += '<div class="card h-100">';
                html += '<div class="card-body">';
                html += '<h5 class="card-title"><i class="bi bi-door-open"></i> ' + unit.unit_number + '</h5>';
                html += '<p class="card-text mb-1"><strong>Type:</strong> ' + (unit.unit_type ? unit.unit_type.name : '-') + '</p>';
                html += '<p class="card-text mb-1"><strong>Floor:</strong> ' + (unit.floor ? unit.floor.name : '-') + '</p>';
                html += '<p class="card-text mb-0"><strong>Block:</strong> ' + (unit.block ? unit.block.name : '-') + '</p>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            listDiv.innerHTML = html;
        }
    </script>
