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
                <a class="nav-link {{ request()->routeIs('program') ? 'active' : '' }}"
                    href="{{ route('program') }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span>{{ __('dashboard.program') }}</span>
                </a>
            </li>

            @canany(['property.view', 'property_info.view', 'user.view', 'role.view'])
            @php
                $companyActive = request()->routeIs(
                    'setup-sidebar.property.index',
                    'setup-sidebar.property-info.index',
                    'setup-sidebar.property-user.index',
                    'setup-sidebar.property-role.index',
                );
            @endphp

            <!-- Company Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#companyCollapse"
                    aria-expanded="{{ $companyActive ? 'true' : 'false' }}" aria-controls="companyCollapse">
                    <i class="fas fa-building me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.company') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $companyActive ? 'show' : '' }}" id="companyCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('property.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.property.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.property.index') }}">
                                <i class="fas fa-building me-2"></i>
                                {{ __('dashboard.property') }}
                            </a>
                        </li>
                        @endcan
                        @can('property_info.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.property-info.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.property-info.index') }}">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('dashboard.property_info') }}
                            </a>
                        </li>
                        @endcan
                        @can('user.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.property-user.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.property-user.index') }}">
                                <i class="fas fa-users me-2"></i>
                                {{ __('dashboard.users') }}
                            </a>
                        </li>
                        @endcan
                        @can('role.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.property-role.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.property-role.index') }}">
                                <i class="fas fa-user-shield me-2"></i>
                                {{ __('dashboard.roles') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['block.view', 'floor.view'])
            @php
                $blocksActive = request()->routeIs('setup-sidebar.blocks.index', 'setup-sidebar.floors.index');
            @endphp

            <!-- Blocks & Floors Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#blocksCollapse"
                    aria-expanded="{{ $blocksActive ? 'true' : 'false' }}" aria-controls="blocksCollapse">
                    <i class="fas fa-building me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.blocks_and_floors') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $blocksActive ? 'show' : '' }}" id="blocksCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('block.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.blocks.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.blocks.index') }}">
                                <i class="fas fa-building me-2"></i>
                                {{ __('dashboard.blocks') }}
                            </a>
                        </li>
                        @endcan
                        @can('floor.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.floors.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.floors.index') }}">
                                <i class="fas fa-layer-group me-2"></i>
                                {{ __('dashboard.floors') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['type_customization.view', 'amenity.view', 'unit.view', 'merge_setting.view', 'base_rate.edit', 'seasonal_rate.view', 'special_rate.view', 'rate_plan.view'])
            @php
                $unitsActive = request()->routeIs(
                    'setup-sidebar.typeCustomization.index',
                    'setup-sidebar.amenity.index',
                    'setup-sidebar.unit.index',
                    'setup-sidebar.merge_setting.index',
                    'setup-sidebar.base_rate.index',
                    'setup-sidebar.seasonal_rate.index',
                    'setup-sidebar.special_rate.index',
                    'setup-sidebar.rate_plan.index',
                );
            @endphp

            <!-- Units Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#unitsCollapse"
                    aria-expanded="{{ $unitsActive ? 'true' : 'false' }}" aria-controls="unitsCollapse">
                    <i class="fas fa-door-open me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.units') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $unitsActive ? 'show' : '' }}" id="unitsCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('type_customization.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.typeCustomization.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.typeCustomization.index') }}">
                                <i class="fas fa-door-open me-2"></i>
                                {{ __('dashboard.unit_type_customization') }}
                            </a>
                        </li>
                        @endcan
                        @can('amenity.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.amenity.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.amenity.index') }}">
                                <i class="fas fa-concierge-bell me-2"></i>
                                {{ __('dashboard.amenities') }}
                            </a>
                        </li>
                        @endcan
                        @can('unit.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.unit.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.unit.index') }}">
                                <i class="fas fa-cubes me-2"></i>
                                {{ __('dashboard.unit_setup') }}
                            </a>
                        </li>
                        @endcan
                        @can('merge_setting.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.merge_setting.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.merge_setting.index') }}">
                                <i class="fas fa-object-group me-2"></i>
                                {{ __('dashboard.merge_settings') }}
                            </a>
                        </li>
                        @endcan
                        @can('base_rate.edit')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.base_rate.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.base_rate.index') }}">
                                <i class="fas fa-tag me-2"></i>
                                {{ __('dashboard.base_rate') }}
                            </a>
                        </li>
                        @endcan
                        @can('seasonal_rate.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.seasonal_rate.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.seasonal_rate.index') }}">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ __('dashboard.seasonal_rate') }}
                            </a>
                        </li>
                        @endcan
                        @can('special_rate.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.special_rate.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.special_rate.index') }}">
                                <i class="fas fa-percentage me-2"></i>
                                {{ __('dashboard.special_rate') }}
                            </a>
                        </li>
                        @endcan
                        @can('rate_plan.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.rate_plan.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.rate_plan.index') }}">
                                <i class="fas fa-layer-group me-2"></i>
                                {{ __('dashboard.rate_plans') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['bank_account.view', 'cost_center.view', 'security_deposit.update', 'tax_customization.view', 'payment_method.view', 'discount_type.view'])
            @php
                $financialActive = request()->routeIs(
                    'setup-sidebar.bank_account.index',
                    'setup-sidebar.cost_center.index',
                    'setup-sidebar.security_deposit.index',
                    'setup-sidebar.taxes.index',
                    'setup-sidebar.payments.index',
                    'setup-sidebar.discount.index',
                );
            @endphp

            <!-- Financial Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#financialCollapse"
                    aria-expanded="{{ $financialActive ? 'true' : 'false' }}">
                    <i class="fas fa-coins me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.financials') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $financialActive ? 'show' : '' }}" id="financialCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('bank_account.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.bank_account.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.bank_account.index') }}">
                                <i class="fas fa-university me-2"></i>
                                {{ __('dashboard.bank_accounts') }}
                            </a>
                        </li>
                        @endcan
                        @can('cost_center.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.cost_center.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.cost_center.index') }}">
                                <i class="fas fa-project-diagram me-2"></i>
                                {{ __('dashboard.cost_centers') }}
                            </a>
                        </li>
                        @endcan
                        @can('security_deposit.update')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.security_deposit.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.security_deposit.index') }}">
                                <i class="fas fa-shield-alt me-2"></i>
                                {{ __('dashboard.security_deposit') }}
                            </a>
                        </li>
                        @endcan
                        @can('tax_customization.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.taxes.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.taxes.index') }}">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                {{ __('dashboard.taxes_and_fees') }}
                            </a>
                        </li>
                        @endcan
                        {{-- <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.currencies.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.currencies.index') }}">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                {{ __('dashboard.currencies') }}
                            </a>
                        </li> --}}
                        @can('payment_method.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.payments.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.payments.index') }}">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ __('dashboard.payment_methods') }}
                            </a>
                        </li>
                        @endcan
                        @can('discount_type.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.discount.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.discount.index') }}">
                                <i class="fas fa-percentage me-2"></i>
                                {{ __('dashboard.discount_types') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['date_setting.edit', 'reservation_source.view', 'guest_class.view', 'sms.send', 'shomoos_setting.view', 'ntmp_setting.view', 'property_facility.view', 'theme_customization.edit'])
            @php
                $generalSettingActive = request()->routeIs(
                    'setup-sidebar.date.index',
                    'setup-sidebar.reservation_source.index',
                    'setup-sidebar.guest_class.index',
                    'setup-sidebar.auto_sms.index',
                    'setup-sidebar.shomoos.index',
                    'setup-sidebar.ntmp.index',
                    'setup-sidebar.property_facility.index',
                    'setup-sidebar.theme_customization.index',
                );
            @endphp

            <!-- General Settings Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#generalSettingCollapse"
                    aria-expanded="{{ $generalSettingActive ? 'true' : 'false' }}">
                    <i class="fas fa-cogs me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.general_settings') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $generalSettingActive ? 'show' : '' }}" id="generalSettingCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('date_setting.edit')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.date.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.date.index') }}">
                                <i class="fas fa-clock me-2"></i>
                                {{ __('dashboard.date_and_time_setting') }}
                            </a>
                        </li>
                        @endcan
                        @can('reservation_source.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.reservation_source.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.reservation_source.index') }}">
                                <i class="fas fa-share-alt me-2"></i>
                                {{ __('dashboard.reservation_sources') }}
                            </a>
                        </li>
                        @endcan
                        @can('guest_class.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.guest_class.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.guest_class.index') }}">
                                <i class="fas fa-users me-2"></i>
                                {{ __('dashboard.guest_classes') }}
                            </a>
                        </li>
                        @endcan
                        {{-- @can('loyalty_setting.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.loyalty_program.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.loyalty_program.index') }}">
                                <i class="fas fa-gift me-2"></i>
                                {{ __('dashboard.loyality_program_settings') }}
                            </a>
                        </li>
                        @endcan --}}
                        @can('sms.send')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.auto_sms.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.auto_sms.index') }}">
                                <i class="fas fa-sms me-2"></i>
                                {{ __('dashboard.sms_auto_send') }}
                            </a>
                        </li>
                        @endcan
                        @can('shomoos_setting.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.shomoos.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.shomoos.index') }}">
                                <i class="fas fa-shield-alt me-2"></i>
                                Shomoos
                            </a>
                        </li>
                        @endcan
                        @can('ntmp_setting.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.ntmp.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.ntmp.index') }}">
                                <i class="fas fa-id-card me-2"></i>
                                Saudi NTMP
                            </a>
                        </li>
                        @endcan
                        @can('property_facility.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.property_facility.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.property_facility.index') }}">
                                <i class="fas fa-hotel me-2"></i>
                                {{ __('dashboard.properties_facilities') }}
                            </a>
                        </li>
                        @endcan
                        @can('theme_customization.edit')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.theme_customization.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.theme_customization.index') }}">
                                <i class="fas fa-palette me-2"></i>
                                {{ __('dashboard.theme_customization') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['printing_option.edit'])
            @php
                $reportingActive = request()->routeIs(
                    'setup-sidebar.print_option.index',
                );
            @endphp

            <!-- Reporting Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#reportingCollapse"
                    aria-expanded="{{ $reportingActive ? 'true' : 'false' }}">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.reporting') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $reportingActive ? 'show' : '' }}" id="reportingCollapse">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.print_option.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.print_option.index') }}">
                                <i class="fas fa-print me-2"></i>
                                {{ __('dashboard.printing_options') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['outlet_setup.view', 'item_categories.view', 'outlet_item.view'])
            @php
                $outletActive = request()->routeIs(
                    'setup-sidebar.outlet_setup.index',
                    'setup-sidebar.item_category.index',
                    'setup-sidebar.items.index',
                );
            @endphp

            <!-- Outlets Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#outletCollapse"
                    aria-expanded="{{ $outletActive ? 'true' : 'false' }}">
                    <i class="fas fa-store me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.outlets') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $outletActive ? 'show' : '' }}" id="outletCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('outlet_setup.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.outlet_setup.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.outlet_setup.index') }}">
                                <i class="fas fa-cogs me-2"></i>
                                {{ __('dashboard.outlet_setup') }}
                            </a>
                        </li>
                        @endcan
                        @can('item_categories.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.item_category.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.item_category.index') }}">
                                <i class="fas fa-layer-group me-2"></i>
                                {{ __('dashboard.items_categories') }}
                            </a>
                        </li>
                        @endcan
                        @can('outlet_item.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.items.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.items.index') }}">
                                <i class="fas fa-boxes me-2"></i>
                                {{ __('dashboard.items') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['terms_and_condition.view', 'penalties.view', 'setup_reservation.edit', 'cancel_reason.view', 'night_audit.start', 'guest_feedback.view'])
            @php
                $rulesActive = request()->routeIs(
                    'setup-sidebar.condition.index',
                    'setup-sidebar.penalty.index',
                    'setup-sidebar.setup_reservation.index',
                    'setup-sidebar.cancel_reason.index',
                    'setup-sidebar.night_audit.index',
                    'setup-sidebar.guest_feedback.index',
                );
            @endphp

            <!-- Rules Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#rulesCollapse"
                    aria-expanded="{{ $rulesActive ? 'true' : 'false' }}">
                    <i class="fas fa-gavel me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.rules') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $rulesActive ? 'show' : '' }}" id="rulesCollapse">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.condition.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.condition.index') }}">
                                <i class="fas fa-file-contract me-2"></i>
                                {{ __('dashboard.terms_and_conditions') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.penalty.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.penalty.index') }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('dashboard.penalties') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.setup_reservation.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.setup_reservation.index') }}">
                                <i class="fas fa-calendar-check me-2"></i>
                                {{ __('dashboard.reservation') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.cancel_reason.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.cancel_reason.index') }}">
                                <i class="fas fa-user-times me-2"></i>
                                {{ __('dashboard.cancel_no_show_reason') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.night_audit.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.night_audit.index') }}">
                                <i class="fas fa-moon me-2"></i>
                                {{ __('dashboard.night_audit_settings') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.guest_feedback.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.guest_feedback.index') }}">
                                <i class="fas fa-comment-dots me-2"></i>
                                {{ __('dashboard.guest_feedback_metrics') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['housekeeper_list.view', 'staff_attendance.view', 'task_type.view'])
            @php
                $housekeepingActive = request()->routeIs(
                    'setup-sidebar.housekeeping_setting.index',
                    'setup-sidebar.staff_attendance.index',
                    'setup-sidebar.task_type.index',
                );
            @endphp

            <!-- Housekeeping Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#housekeepingCollapse"
                    aria-expanded="{{ $housekeepingActive ? 'true' : 'false' }}">
                    <i class="fas fa-broom me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.housekeeping_settings') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $housekeepingActive ? 'show' : '' }}" id="housekeepingCollapse">
                    <ul class="nav flex-column ps-3">
                        @can('housekeeper_list.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.housekeeping_setting.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.housekeeping_setting.index') }}">
                                <i class="fas fa-users me-2"></i>
                                {{ __('dashboard.housekeepers_list') }}
                            </a>
                        </li>
                        @endcan
                        @can('staff_attendance.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.staff_attendance.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.staff_attendance.index') }}">
                                <i class="fas fa-user-clock me-2"></i>
                                {{ __('dashboard.employee_attendance') }}
                            </a>
                        </li>
                        @endcan
                        @can('task_type.view')
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.task_type.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.task_type.index') }}">
                                <i class="fas fa-tasks me-2"></i>
                                {{ __('dashboard.task_types') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany

            @canany(['manage_product.view', 'manage_inventory.view', 'website_setting.view', 'website_page.view', 'website_faq.view'])
            @php
                $channelActive = request()->routeIs(
                    'setup-sidebar.manage_product.index',
                    'setup-sidebar.manage_inventory.index',
                    'setup-sidebar.website_configuration.index',
                    'setup-sidebar.website_pages.index',
                    'setup-sidebar.website_faq.index',
                );
            @endphp

            <!-- Channel Manager Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="collapse" data-bs-target="#channelCollapse"
                    aria-expanded="{{ $channelActive ? 'true' : 'false' }}">
                    <i class="fas fa-network-wired me-2"></i>
                    <span class="flex-grow-1">{{ __('dashboard.channel_manager') }}</span>
                    <i class="ms-auto dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $channelActive ? 'show' : '' }}" id="channelCollapse">
                    <ul class="nav flex-column ps-3">
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.manage_product.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.manage_product.index') }}">
                                <i class="fas fa-box-open me-2"></i>
                                {{ __('dashboard.manage_products') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.manage_inventory.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.manage_inventory.index') }}">
                                <i class="fas fa-sync-alt me-2"></i>
                                {{ __('dashboard.manage_inventory') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.website_configuration.index') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.website_configuration.index') }}">
                                <i class="fas fa-globe me-2"></i>
                                {{ __('dashboard.website_configuration') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.website_pages.*') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.website_pages.index') }}">
                                <i class="fas fa-file-lines me-2"></i>
                                Website Pages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3 py-2 {{ request()->routeIs('setup-sidebar.website_faq.*') ? 'active' : '' }}"
                                href="{{ route('setup-sidebar.website_faq.index') }}">
                                <i class="fas fa-circle-question me-2"></i>
                                FAQ Content
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcanany

            @can('subscription.view')
            <!-- subscription -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('setup-sidebar.setup_subscription.index') ? 'active' : '' }}"
                    href="{{ route('setup-sidebar.setup_subscription.index') }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>{{ __('dashboard.subscriptions') }}</span>
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
