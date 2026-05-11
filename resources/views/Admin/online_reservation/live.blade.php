@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = fn ($ar, $en) => $isArabic ? $ar : $en;
    $formatMoney = fn ($value) => 'SAR '.number_format((float) $value, 2);
    $sourceCards = $sourceSnapshots->isNotEmpty()
        ? $sourceSnapshots
        : $websiteSources->map(function ($source) {
            return [
                'id' => $source->id,
                'name' => trim((string) ($source->report_name ?: optional($source->masterSource)->name ?: 'Website')),
                'count' => 0,
                'color' => '#94a3b8',
            ];
        });
    $tabs = [
        [
            'id' => 'pending',
            'active' => true,
            'count' => $pendingCount,
            'label' => __('dashboard.new_online_reservation_request'),
            'small' => $copy('حجوزات الموقع بانتظار المراجعة', 'Website bookings waiting for review'),
            'rows' => $pendingReservations,
            'empty' => $copy('لا توجد حجوزات موقع بانتظار المراجعة حالياً.', 'No website bookings are currently waiting for review.'),
        ],
        [
            'id' => 'accepted',
            'active' => false,
            'count' => $acceptedCount,
            'label' => __('dashboard.new_online_accepted_requests'),
            'small' => $copy('حجوزات الموقع المؤكدة أو النشطة', 'Confirmed or active website bookings'),
            'rows' => $acceptedReservations,
            'empty' => $copy('لا توجد حجوزات موقع مؤكدة أو نشطة ضمن التصفية الحالية.', 'No confirmed or active website bookings were found for the current filter.'),
        ],
        [
            'id' => 'declined',
            'active' => false,
            'count' => $declinedCount,
            'label' => __('dashboard.new_declined_request'),
            'small' => $copy('حجوزات الموقع الملغاة أو عدم الحضور', 'Cancelled or no-show website bookings'),
            'rows' => $declinedReservations,
            'empty' => $copy('لا توجد حجوزات موقع ملغاة أو عدم حضور ضمن التصفية الحالية.', 'No cancelled or no-show website bookings were found for the current filter.'),
        ],
    ];
@endphp

@section('title', 'Online Reservation')

<style>
    .online-live .hero-card {
        border: 0;
        border-radius: 1.25rem;
        color: #fff;
        background: linear-gradient(135deg, #1d4ed8, #0f766e);
    }

    .online-live .hero-pill,
    .online-live .source-tag,
    .online-live .status-chip,
    .online-live .source-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 600;
    }

    .online-live .hero-pill {
        padding: .45rem .8rem;
        background: rgba(255, 255, 255, .16);
        color: #fff;
    }

    .online-live .source-tag,
    .online-live .source-chip {
        padding: .4rem .75rem;
        border: 1px solid rgba(15, 23, 42, .08);
        background: #f8fafc;
        color: #334155;
    }

    .online-live .source-chip::before,
    .online-live .status-chip::before,
    .online-live .source-dot::before {
        content: '';
        width: .55rem;
        height: .55rem;
        border-radius: 999px;
        background: var(--chip-color, currentColor);
    }

    .online-live .status-chip {
        padding: .45rem .75rem;
    }

    .online-live .status-chip--pending {
        color: #b45309;
        background: rgba(245, 158, 11, .14);
    }

    .online-live .status-chip--accepted {
        color: #15803d;
        background: rgba(34, 197, 94, .14);
    }

    .online-live .status-chip--declined {
        color: #b91c1c;
        background: rgba(239, 68, 68, .14);
    }

    .online-live .guest-badge {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: .9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(37, 99, 235, .16), rgba(14, 116, 144, .08));
        color: #1d4ed8;
        font-weight: 700;
    }

    .online-live .summary-card,
    .online-live .panel-card {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 1.1rem;
    }

    .online-live .summary-value {
        font-size: 1.55rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .online-live .summary-label,
    .online-live .subtle-copy {
        color: #64748b;
    }

    .online-live .table thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        background: #f8fafc;
        white-space: nowrap;
    }
</style>

@section('content')
    <main class="online-live">
        <div class="row g-3 mb-4">
            <div class="col-xl-8">
                <div class="card hero-card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-uppercase small opacity-75 mb-2">{{ __('dashboard.online_reservation') }}</div>
                        <h2 class="h3 fw-bold mb-3">{{ $copy('مساحة عمل حجوزات الموقع', 'Website Reservation Workspace') }}</h2>
                        <p class="mb-0 opacity-75">
                            {{ $copy('تظهر هنا الحجوزات القادمة من واجهة الحجز في الموقع مباشرة حتى يتمكن الفريق من مراجعتها ومتابعتها من نفس نظام الحجوزات.', 'This page shows reservations created from your public booking website so the team can review and continue them inside the main reservation workflow.') }}
                        </p>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <span class="hero-pill"><i class="bi bi-lightning-charge"></i>{{ $pendingCount }} {{ $copy('بحاجة إلى مراجعة', 'need review') }}</span>
                            <span class="hero-pill"><i class="bi bi-globe2"></i>{{ $totalWebsiteReservations }} {{ $copy('حجز موقع', 'website bookings') }}</span>
                            <span class="hero-pill"><i class="bi bi-cash-coin"></i>{{ $copy('قيمة الانتظار', 'Pending value') }} {{ $formatMoney($queueValue) }}</span>
                            <span class="hero-pill">
                                <i class="bi bi-clock-history"></i>
                                @if ($latestWebsiteBookingAt)
                                    {{ $copy('آخر حجز', 'Latest booking') }} {{ $latestWebsiteBookingAt->diffForHumans() }}
                                @else
                                    {{ $copy('بانتظار أول حجز موقع', 'Waiting for first website booking') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card panel-card shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-2">{{ $copy('ملخص مصادر الموقع', 'Website source snapshot') }}</h3>
                        <p class="subtle-copy small mb-3">{{ $copy('المصادر التي تسجل حجوزات الموقع في النظام حالياً.', 'Sources currently recording website reservations in the system.') }}</p>

                        <div class="d-grid gap-2">
                            @forelse ($sourceCards as $source)
                                <div class="d-flex align-items-center justify-content-between rounded-4 border bg-light-subtle px-3 py-2">
                                    <div class="d-flex align-items-center gap-2 fw-semibold">
                                        <span class="source-dot d-inline-flex align-items-center" style="--chip-color:{{ $source['color'] }};"></span>
                                        <span>{{ $source['name'] }}</span>
                                    </div>
                                    <span class="subtle-copy small">{{ $source['count'] }} {{ $copy('حجز', 'bookings') }}</span>
                                </div>
                            @empty
                                <div class="rounded-4 border bg-light-subtle px-3 py-3 subtle-copy small">
                                    {{ $copy('لا يوجد مصدر موقع مهيأ حتى الآن.', 'No website source has been configured yet.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (! $hasWebsiteSources)
            <div class="alert alert-info rounded-4 border-0 shadow-sm mb-4">
                <strong>{{ $copy('معلومة:', 'Heads up:') }}</strong>
                @if ($hasUnassignedWebsiteReservations)
                    {{ $copy('لا يوجد حالياً مصدر حجوزات باسم Website في الإعدادات، لكن الحجوزات المباشرة القادمة من الموقع ما زالت تظهر هنا تحت اسم Direct Website إلى أن تتم إضافة المصدر وربطه.', 'There is no Website reservation source configured yet, but direct bookings from the public site are still shown here under Direct Website until the source is added and linked.') }}
                @else
                    {{ $copy('لا يوجد حالياً مصدر حجوزات باسم Website في الإعدادات. عند وصول حجوزات مباشرة من الموقع ستظهر هنا تحت اسم Direct Website إلى أن تتم إضافة المصدر وربطه.', 'There is no Website reservation source configured yet. When direct bookings arrive from the public site, they will still appear here under Direct Website until the source is added and linked.') }}
                @endif
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-sm-6">
                <div class="card summary-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="summary-label mb-2">{{ $copy('قائمة الانتظار', 'Pending queue') }}</div>
                        <div class="summary-value">{{ $pendingCount }}</div>
                        <div class="subtle-copy small mt-2">{{ $copy('حجوزات موقع pending', 'Website reservations in pending status') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card summary-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="summary-label mb-2">{{ $copy('مؤكد أو نشط', 'Confirmed or active') }}</div>
                        <div class="summary-value">{{ $acceptedCount }}</div>
                        <div class="subtle-copy small mt-2">{{ $copy('مؤكد أو مقيم أو مكتمل', 'Confirmed, checked-in, or checked-out') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card summary-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="summary-label mb-2">{{ $copy('ملغي أو عدم حضور', 'Cancelled or no-show') }}</div>
                        <div class="summary-value">{{ $declinedCount }}</div>
                        <div class="subtle-copy small mt-2">{{ $copy('تم إلغاؤه أو لم يصل الضيف', 'Cancelled or the guest did not arrive') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card summary-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="summary-label mb-2">{{ $copy('مصادر الموقع النشطة', 'Active website sources') }}</div>
                        <div class="summary-value">{{ $activeSourceCount }}</div>
                        <div class="subtle-copy small mt-2">{{ $copy('مصادر ظهرت ضمن النتائج الحالية', 'Sources represented in the current results') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card panel-card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3 mb-3">
                    <div>
                        <h3 class="h6 fw-bold mb-1">{{ $copy('فلاتر حجوزات الموقع', 'Website booking filters') }}</h3>
                        <p class="subtle-copy mb-0">{{ $copy('ابحث باسم الضيف أو رقم الحجز، وفلتر حسب مصدر الموقع أو نافذة الوصول.', 'Search by guest name or reservation number, then filter by website source or arrival window.') }}</p>
                    </div>
                    <button class="btn btn-outline-secondary" type="button" id="filterToggleBtn">
                        <i class="bi bi-funnel me-1"></i>{{ __('dashboard.filter') }}
                    </button>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @forelse ($sourceCards->take(4) as $source)
                        <span class="source-tag">{{ $source['name'] }}</span>
                    @empty
                        <span class="source-tag">{{ $copy('حجوزات الموقع المباشرة', 'Direct website bookings') }}</span>
                    @endforelse
                </div>

                <form method="GET" action="{{ route('dashboard.online_reservation.index') }}">
                    <div id="filterPanel" class="{{ $filtersOpen ? '' : 'd-none' }}">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.guest') }}</label>
                                <input type="text" name="guest" value="{{ $filters['guest'] ?? '' }}" class="form-control"
                                    placeholder="{{ $copy('اسم الضيف أو رقم الحجز', 'Guest name or reservation number') }}">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ $copy('مصدر الموقع', 'Website source') }}</label>
                                <select name="source_id" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($websiteSources as $source)
                                        <option value="{{ $source->id }}" {{ (string) ($filters['source_id'] ?? '') === (string) $source->id ? 'selected' : '' }}>
                                            {{ trim((string) ($source->report_name ?: optional($source->masterSource)->name ?: 'Website')) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.from') }}</label>
                                <input type="date" name="arrival_from" value="{{ $filters['arrival_from'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.to') }}</label>
                                <input type="date" name="arrival_to" value="{{ $filters['arrival_to'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-lg-1 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">{{ __('dashboard.search') }}</button>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('dashboard.online_reservation.index') }}" class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card panel-card shadow-sm">
            <div class="card-body p-0">
                <ul class="nav nav-tabs px-3 pt-3 border-0" role="tablist">
                    @foreach ($tabs as $tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab['active'] ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#{{ $tab['id'] }}" type="button">
                                <span class="fw-bold me-2">{{ $tab['count'] }}</span>{{ $tab['label'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content p-3">
                    @foreach ($tabs as $tab)
                        <div class="tab-pane fade {{ $tab['active'] ? 'show active' : '' }}" id="{{ $tab['id'] }}">
                            <div class="subtle-copy small mb-3">{{ $tab['small'] }}</div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.res_no') }}</th>
                                            <th>{{ __('dashboard.guest') }}</th>
                                            <th>{{ __('dashboard.from') }} / {{ __('dashboard.to') }}</th>
                                            <th>{{ __('dashboard.nights') }}</th>
                                            <th>{{ $copy('المصدر', 'Source') }}</th>
                                            <th>{{ __('dashboard.amount') }}</th>
                                            <th>{{ $copy('الحالة', 'Status') }}</th>
                                            <th class="text-end">{{ __('dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tab['rows'] as $reservation)
                                            <tr>
                                                <td class="fw-semibold text-primary">{{ $reservation['reservation_number'] }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="guest-badge">{{ $reservation['guest_initials'] }}</span>
                                                        <div>
                                                            <div class="fw-semibold">{{ $reservation['guest_name'] }}</div>
                                                            <div class="subtle-copy small">{{ $reservation['guest_sub'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $reservation['stay_from'] }}</div>
                                                    <div class="subtle-copy small">{{ $reservation['stay_to'] }} · {{ $reservation['unit_label'] }}</div>
                                                </td>
                                                <td>{{ $reservation['nights'] }}</td>
                                                <td>
                                                    <span class="source-chip" style="--chip-color:{{ $reservation['source_color'] }};">{{ $reservation['source_name'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $formatMoney($reservation['amount']) }}</div>
                                                    <div class="subtle-copy small">{{ $reservation['amount_note'] }}</div>
                                                </td>
                                                <td>
                                                    <span class="status-chip status-chip--{{ $reservation['status_bucket'] }}">{{ $reservation['status_label'] }}</span>
                                                </td>
                                                <td class="text-end">
                                                    @if ($reservation['edit_url'])
                                                        <a href="{{ $reservation['edit_url'] }}" class="btn btn-sm btn-outline-primary">{{ $copy('فتح الحجز', 'Open reservation') }}</a>
                                                    @else
                                                        <span class="subtle-copy small">{{ $copy('بدون صلاحية تعديل', 'No edit access') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="fw-semibold mb-2">{{ $tab['empty'] }}</div>
                                                    <div class="subtle-copy">{{ $copy('ستظهر هنا أي حجوزات محفوظة من واجهة الموقع مباشرة، وحتى إذا لم يكن مصدر Website مضافاً بعد فسيتم عرضها باسم Direct Website.', 'Any reservations created from the public website booking flow will appear here, and if a Website source is not configured yet they will be shown as Direct Website.') }}</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('filterToggleBtn');
            const filterPanel = document.getElementById('filterPanel');

            if (!toggleBtn || !filterPanel) {
                return;
            }

            toggleBtn.addEventListener('click', function() {
                filterPanel.classList.toggle('d-none');
            });
        });
    </script>
@endpush
