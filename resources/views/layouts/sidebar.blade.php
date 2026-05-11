@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

<style>
    /* Normal link */
    .nav-link {
        transition: all 0.2s ease-in-out;
        border-radius: 6px;
    }

    /* Hover color */
    .nav-link:hover {
        background-color: {{ $theme->sidebar_hover_color }} !important;
        color: {{ $theme->sidebar_text_color }} !important;
    }

    /* Active (selected tab) */
    .nav-link.active {
        background-color: {{ $theme->sidebar_active_color }} !important;
        color: {{ $theme->sidebar_text_color }} !important;
    }
</style>

<nav id="sidebar" class="{{ app()->isLocale('ar') ? 'rtl-sidebar' : 'ltr-sidebar' }}"
    style="background-color: {{ $theme->sidebar_bg_color }};">
    <div class="sidebar-header">
        <img src="{{ $theme->logo ? asset($theme->logo) : asset('logo.webp') }}" alt="Logo" class="logo-img">
        {{-- <h3 class="logo-title">{{ __('dashboard.bit') }}</h3> --}}
        {{-- <p class="logo-subtitle">{{ __('dashboard.bit') }}</p> --}}
    </div>

    <div class="sidebar-menu">
        <ul class="list-unstyled">

            <!-- Program -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('program') ? 'active' : '' }}" href="{{ route('program') }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span>{{ __('dashboard.program') }}</span>
                </a>
            </li>
            <!-- Dashboard -->
            @can('dashboard.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>{{ __('dashboard.dashboard') }}</span>
                </a>
            </li>
@endcan

            <!-- Reservation -->
            @can('reservation.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.reservation.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.reservation.index') }}">
                    <i class="fas fa-calendar-check me-2"></i>
                    <span>{{ __('dashboard.reservation') }}</span>
                </a>
            </li>
            @endcan


            <!-- Unit Status -->
          @can('unit_status.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.unit_status.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.unit_status.index') }}">
                    <i class="fas fa-building me-2"></i>
                    <span>{{ __('dashboard.unit_status') }}</span>
                </a>
            </li>
            @endcan
        @canany(['housekeeping_task.view', 'housekeeper_list.view'])
        @php
            $housekeepingActive =
                request()->routeIs('dashboard.housekeeping_status.index') ||
                request()->routeIs('dashboard.housekeeping_task.index');
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="collapse" data-bs-target="#housekeepingCollapse"
                aria-expanded="{{ $housekeepingActive ? 'true' : 'false' }}">
                <i class="fas fa-broom me-2"></i>
                <span class="flex-grow-1">{{ __('dashboard.housekeeping') }}</span>
                <i class="ms-auto dropdown-arrow"></i>
            </a>

            <div class="collapse {{ $housekeepingActive ? 'show' : '' }}" id="housekeepingCollapse">
                <ul class="nav flex-column ps-3">

                    @can('housekeeping_task.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.housekeeping_status.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.housekeeping_status.index') }}">
                            <i class="fas fa-clipboard-check me-2"></i>
                            {{ __('dashboard.status') }}
                        </a>
                    </li>
                    @endcan

                    @can('housekeeper_list.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.housekeeping_task.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.housekeeping_task.index') }}">
                            <i class="fas fa-tasks me-2"></i>
                            {{ __('dashboard.housekeeping_tasks') }}
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
        </li>
        @endcanany


        @canany(['receipt.view', 'payment.view', 'promissory_note.view', 'invoice.view', 'credit_notes.print', 'drop_cash.view'])
        @php
            $voucherActive = request()->routeIs([
                'dashboard.receipt.index',
                'dashboard.payment.index',
                'dashboard.promissory.index',
                'dashboard.invoice.index',
                'dashboard.credit.index',
                'dashboard.drop_cash.index',
            ]);
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="collapse" data-bs-target="#voucherCollapse"
                aria-expanded="{{ $voucherActive ? 'true' : 'false' }}">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                <span class="flex-grow-1">{{ __('dashboard.vouchers') }}</span>
                <i class="ms-auto dropdown-arrow"></i>
            </a>

            <div class="collapse {{ $voucherActive ? 'show' : '' }}" id="voucherCollapse">
                <ul class="nav flex-column ps-3">

                    @can('receipt.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.receipt.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.receipt.index') }}">
                            <i class="fas fa-receipt me-2"></i>
                            {{ __('dashboard.receipt') }}
                        </a>
                    </li>
                    @endcan

                    @can('payment.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.payment.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.payment.index') }}">
                            <i class="fas fa-file-signature me-2"></i>
                            {{ __('dashboard.payment') }}
                        </a>
                    </li>
                    @endcan

                    @can('promissory_note.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.promissory.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.promissory.index') }}">
                            <i class="fas fa-file-signature me-2"></i>
                            {{ __('dashboard.promissory_notes') }}
                        </a>
                    </li>
                    @endcan

                    @can('invoice.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.invoice.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.invoice.index') }}">
                            <i class="fas fa-file-invoice me-2"></i>
                            {{ __('dashboard.invoices') }}
                        </a>
                    </li>
                    @endcan

                    @can('credit_notes.print')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.credit.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.credit.index') }}">
                            <i class="fas fa-file-invoice me-2"></i>
                            {{ __('dashboard.credit_notes') }}
                        </a>
                    </li>
                    @endcan

                    @can('drop_cash.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.drop_cash.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.drop_cash.index') }}">
                            <i class="fas fa-cash-register me-2"></i>
                            {{ __('dashboard.drop_cash') }}
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
        </li>
        @endcanany

        @canany(['outlet_setup.view', 'outlet_item.view'])
        @php
            $outletActive = request()->routeIs(['dashboard.outlet_property.index', 'dashboard.outlet_order.index']);
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="collapse" data-bs-target="#outletCollapse"
                aria-expanded="{{ $outletActive ? 'true' : 'false' }}">
                <i class="fas fa-store-alt me-2"></i>
                <span class="flex-grow-1">{{ __('dashboard.outlets') }}</span>
                <i class="ms-auto dropdown-arrow"></i>
            </a>

            <div class="collapse {{ $outletActive ? 'show' : '' }}" id="outletCollapse">
                <ul class="nav flex-column ps-3">

                    @can('outlet_setup.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.outlet_property.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.outlet_property.index') }}">
                            <i class="fas fa-store me-2"></i>
                            {{ __('dashboard.property_outlets') }}
                        </a>
                    </li>
                    @endcan

                    @can('outlet_item.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.outlet_order.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.outlet_order.index') }}">
                            <i class="fas fa-shopping-bag me-2"></i>
                            {{ __('dashboard.orders_list') }}
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
        </li>
        @endcanany


        @canany(['guest.view', 'corporate.view', 'vendor.view'])
        @php
            $customerActive = request()->routeIs([
                'dashboard.guest.index',
                'dashboard.corporate.index',
                'dashboard.vendor.index',
            ]);
        @endphp

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="collapse" data-bs-target="#customerCollapse"
                aria-expanded="{{ $customerActive ? 'true' : 'false' }}">
                <i class="fas fa-users me-2"></i>
                <span class="flex-grow-1">{{ __('dashboard.customers') }}</span>
                <i class="ms-auto dropdown-arrow"></i>
            </a>

            <div class="collapse {{ $customerActive ? 'show' : '' }}" id="customerCollapse">
                <ul class="nav flex-column ps-3">

                    @can('guest.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.guest.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.guest.index') }}">
                            <i class="fas fa-user me-2"></i>
                            {{ __('dashboard.guests') }}
                        </a>
                    </li>
                    @endcan

                    @can('corporate.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.corporate.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.corporate.index') }}">
                            <i class="fas fa-building me-2"></i>
                            {{ __('dashboard.corporates') }}
                        </a>
                    </li>
                    @endcan

                    @can('vendor.view')
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ request()->routeIs('dashboard.vendor.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.vendor.index') }}">
                            <i class="fas fa-handshake me-2"></i>
                            {{ __('dashboard.vendors') }}
                        </a>
                    </li>
                    @endcan

                </ul>
            </div>
        </li>
        @endcanany

        @can('sms.send')
        @php
            $smsActive = request()->routeIs('dashboard.manual_sms.index');
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="collapse" data-bs-target="#smsCollapse"
                aria-expanded="{{ $smsActive ? 'true' : 'false' }}">
                <i class="fas fa-sms me-2"></i>
                <span class="flex-grow-1">{{ __('dashboard.sms') }}</span>
                <i class="ms-auto dropdown-arrow"></i>
            </a>

            <div class="collapse {{ $smsActive ? 'show' : '' }}" id="smsCollapse">
                <ul class="nav flex-column ps-3">
                    <li class="nav-item">
                        <a class="nav-link ps-3 py-2 {{ $smsActive ? 'active' : '' }}"
                            href="{{ route('dashboard.manual_sms.index') }}">
                            <i class="fas fa-paper-plane me-2"></i>
                            {{ __('dashboard.send_manual_sms') }}
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endcan

        @can('cash_drawer_balance.view')
        <!-- Cash Drawer Balance -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.cash_drawer.index') ? 'active' : '' }}"
                href="{{ route('dashboard.cash_drawer.index') }}">
                <i class="fas fa-cash-register"></i>
                <span>{{ __('dashboard.cash_drawer_balance') }}</span>
            </a>
        </li>
        @endcan

        @can('reports.view')
        <!-- Reports -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.reports.index') ? 'active' : '' }}"
                href="{{ route('dashboard.reports.index') }}">
                <i class="fas fa-chart-bar"></i>
                <span>{{ __('dashboard.reports') }}</span>
            </a>
        </li>
        @endcan

        @can('logs.view')
        <!-- Logs -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.logs.index') ? 'active' : '' }}"
                href="{{ route('dashboard.logs.index') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>{{ __('dashboard.logs') }}</span>
            </a>
        </li>
        @endcan

        @can('night_audit.start')
        <!-- Night Audit -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.night_audit.index') ? 'active' : '' }}"
                href="{{ route('dashboard.night_audit.index') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>{{ __('dashboard.night_audit') }}</span>
            </a>
        </li>
        @endcan


        </ul>
    </div>
</nav>

<!-- Mobile Toggle Button -->
<button id="sidebarToggle" class="btn btn-primary d-lg-none">
    <i class="fas fa-bars"></i>
</button>
