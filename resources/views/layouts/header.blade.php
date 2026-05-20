@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $user = auth()->user();
    $property = $currentProperty ?? app(\App\Support\PropertyContext::class)->property();
    $canSwitchProperty = $user
        && ! $user->isSuperAdmin()
        && (
            $user->isTenantOwner()
            || (isset($accessibleProperties) && $accessibleProperties->count() > 1)
        );
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    $unreadCount = $notifications->whereNull('read_at')->count();
    $supportUnreadCount = $user && ! $user->isSuperAdmin()
        ? app(\App\Support\SupportTicketUnreadCounter::class)->forTenantArea('reservation')
        : 0;
    $hrModulePermissions = [
        'manage_dashboard',
        'manage_employee',
        'generate_card',
        'manage_attendance',
        'manage_payroll',
        'manage_finance',
        'manage_documents',
        'manage_branch',
        'manage_notification',
        'manage_setting',
        'manage_reports',
        'manage_warehouse',
    ];
@endphp

<header class="top-navbar app-header shadow-sm py-3">
    <div class="container-fluid">
        <div class="app-header__bar d-flex justify-content-between align-items-center">
            <!-- Right Side: Search, Language, Profile -->
            <div class="app-header__actions d-flex align-items-center gap-3 ms-auto">
                <!-- Search Bar - In Middle -->
                {{-- <div class="search-container position-relative" style="max-width: 300px;">
                    <input type="text" class="form-control search-input"
                        placeholder="{{ __('dashboard.type_guest_phone_number') }}" id="globalSearch">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                </div> --}}

                <!-- Compact Tabs (Icons Only on Mobile) -->
                <div class="app-header__tabs d-flex align-items-center gap-2" style="{{ app()->getLocale() == 'ar' ? 'margin-right:120px;' : 'margin-left:70px;' }}">
                    @if($canSwitchProperty && isset($accessibleProperties) && $accessibleProperties->isNotEmpty())
                        <form method="POST" action="{{ route('current-property.update') }}" class="property-switcher d-flex align-items-center gap-2">
                            @csrf
                            <select name="current_property_id" class="property-switcher__select form-select form-select-sm" onchange="this.form.submit()">
                                @foreach($accessibleProperties as $accessibleProperty)
                                    <option value="{{ $accessibleProperty->id }}" @selected(($property?->id ?? null) === $accessibleProperty->id)>
                                        {{ $accessibleProperty->property_name_en ?? $accessibleProperty->property_name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <form class="property-switcher d-flex align-items-center gap-2">
                            <select class="property-switcher__select form-select form-select-sm" disabled>
                                <option>{{ __('dashboard.properties') }}</option>
                            </select>
                        </form>
                    @endif

                    <!-- Switch to HR -->
                    @canany($hrModulePermissions)
                    <a href="{{ route('dashboard.program') }}"
                        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white">
                        <i class="fas fa-users-gear"></i>
                        <span class="d-none d-lg-inline">{{ __('dashboard.hr_management') }}</span>
                    </a>
                    @endcanany

                    <!-- Online Reservation -->
                    @can('reservation.view')
                    <a href="{{ route('dashboard.online_reservation.index') }}"
                        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white">
                        <i class="fas fa-globe"></i>
                        <span class="d-none d-lg-inline">{{ __('dashboard.reservation') }}</span>
                    </a>
                    @endcan

                    <!-- Setup -->
                    @can('dashboard.view')
                    <a href="{{ route('setup-sidebar') }}"
                        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white">
                        <i class="fas fa-cog"></i>
                        <span class="d-none d-lg-inline">{{ __('dashboard.setting') }}</span>
                    </a>
                    @endcan

                    <!-- Subscriptions -->
                    @can('subscription.view')
                    <a href="{{ route('setup-sidebar.setup_subscription.index') }}"
                        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white">
                        <i class="fas fa-crown"></i>
                        <span class="d-none d-lg-inline">{{ __('dashboard.subscriptions') }}</span>
                    </a>
                    @endcan

                    <!-- Notification Button (Same style as Support Center) -->
                    <div class="dropdown">
                        <button
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white position-relative"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="fas fa-bell"></i>
                            <span class="d-none d-lg-inline">
                                {{ __('dashboard.notifications') }}
                            </span>

                            @if($unreadCount > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size: 0.6rem;">
                                {{ $unreadCount }}
                            </span>
                            @endif
                        </button>

                        <!-- Dropdown -->
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                            style="min-width: 320px; max-height: 400px; overflow-y: auto;">

                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ __('dashboard.notifications') }}</h6>
                                @if($unreadCount > 0)
                                <small class="text-muted">
                                    {{ __('You have :count new notifications', ['count' => $unreadCount]) }}
                                </small>
                                @endif
                            </div>

                            <div class="list-group list-group-flush">
                                @forelse($notifications as $notification)
                                <a href="#" class="list-group-item list-group-item-action border-0 py-3 px-4 {{ is_null($notification->read_at) ? 'bg-light' : '' }}"
                                    onclick="markAsRead({{ $notification->id }})">
                                    <div class="d-flex gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px; background-color: {{ $notification->type == 'arrival' ? '#10b981' : ($notification->type == 'departure' ? '#f59e0b' : ($notification->type == 'payment' ? '#6366f1' : '#6b7280')) }}20;">
                                            <i class="fas fa-{{ $notification->type == 'arrival' ? 'sign-in-alt' : ($notification->type == 'departure' ? 'sign-out-alt' : ($notification->type == 'payment' ? 'dollar-sign' : ($notification->type == 'check_in' ? 'user-check' : ($notification->type == 'check_out' ? 'user-times' : 'calendar-plus')))) }}"
                                                style="color: {{ $notification->type == 'arrival' ? '#10b981' : ($notification->type == 'departure' ? '#f59e0b' : ($notification->type == 'payment' ? '#6366f1' : '#6b7280')) }}"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 fw-medium">{{ app()->getLocale() == 'ar' ? $notification->title_ar : $notification->title }}</p>
                                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? $notification->message_ar : $notification->message }}</small>
                                            <br><small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-bell-slash mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0">No notifications</p>
                                </div>
                                @endforelse
                            </div>

                            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="markAllAsRead(event)">
                                    {{ __('dashboard.mark_all_read') }}
                                </button>
                                <a href="{{ route('dashboard.notifications.index') }}" class="text-decoration-none small">
                                    {{ __('dashboard.view_all_notifications') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Support Center -->
                    <a href="{{ route('support.tickets.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 text-white position-relative">
                        <i class="fas fa-headset"></i>
                        <span class="d-none d-lg-inline">{{ __('dashboard.support') }}</span>
                        @if($supportUnreadCount > 0)
                            <span
                                class="reservation-support-unread-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                data-reservation-support-unread-count="{{ $supportUnreadCount }}"
                                style="font-size: 0.6rem;">
                                {{ $supportUnreadCount }}
                            </span>
                        @endif
                    </a>


                </div>



                <!-- Language Switcher as Dropdown -->
                <div class="dropdown language-dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 text-white"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-language"></i>
                        <span class="current-language">
                            {{ app()->getLocale() == 'en' ? 'EN' : 'عربي' }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
                                href="{{ route('locale.switch', 'en') }}">
                                <span class="me-2">🇺🇸</span>
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}"
                                href="{{ route('locale.switch', 'ar') }}">
                                <span class="me-2">🇸🇦</span>
                                عربي
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- User Profile Dropdown - Far Right -->
                <div class="dropdown">
                    <button
                        class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2 text-white"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar bg-primary text-white rounded-circle d-flex
                align-items-center justify-content-center"
                            style="width: 38px; height: 38px; font-size: 1rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="user-name d-none d-md-inline ">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passwordUpdateModal">
                                <i class="fas fa-key {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                {{ __('dashboard.update_password') }}
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                {{ __('dashboard.profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger w-100 text-start">
                                    <i
                                        class="fas fa-sign-out-alt {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                    {{ __('dashboard.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Toggle Button -->
<button id="sidebarToggle" class="btn btn-primary d-lg-none"
    style="position: fixed; top: 1rem; {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 1rem; z-index: 1100;">
    <i class="fas fa-bars"></i>
</button>
@foreach (['success', 'danger', 'warning', 'info'] as $msg)
    @if (session($msg))
        <div class="alert m-3 alert-{{ $msg }} alert-dismissible fade show mt-3" role="alert"
            id="flash-message">
            {{ session($msg) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

<!-- Support Center Modal -->
<div class="modal fade" id="supportCenterModal" tabindex="-1" aria-labelledby="supportCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <h5 class="modal-title text-white" id="supportCenterModalLabel">
                    <i class="fas fa-headset me-2"></i>{{ __('dashboard.support_center') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Headquarter! KSA</h6>
                    <p class="mb-0 text-muted">B-IT, 7036 صيدا, Ad Duraihimiyah, Riyadh 12471</p>
                </div>

                <a href="tel:0547376269" class="btn btn-outline-success mb-3 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-phone"></i>
                    0547376269
                </a>

                <a href="mailto:info@b-it.co" class="btn btn-outline-primary mb-3 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-envelope"></i>
                    info@b-it.co
                </a>

                <a href="https://b-it.co/en" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info mb-3 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-globe"></i>
                    https://b-it.co/en
                </a>

                <hr class="my-4">

                <div class="text-muted small">
                    <p class="mb-1">{{ __('dashboard.software_by') }}</p>
                    <a href="https://b-it.co/en" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <strong>B-IT Solutions</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-hide script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                // Bootstrap 5 dismiss
                const alert = new bootstrap.Alert(flash);
                alert.close();
            }, 3000);
        }

        // Notification dropdown - refresh on open
        const notificationBtn = document.querySelector('[data-bs-toggle="dropdown"][aria-expanded="false"]');
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(btn => {
            btn.addEventListener('show.bs.dropdown', function() {
                if (this.querySelector('.fa-bell')) {
                    loadNotifications();
                }
            });
        });
    });

    function loadNotifications() {
        fetch('{{ route("dashboard.reservation.notifications") }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).catch(() => {});
    }

    function markAsRead(id) {
        fetch('{{ route("dashboard.reservation.notifications.mark_read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        }).then(response => response.json()).catch(() => {});
    }

    function markAllAsRead(event) {
        event.preventDefault();
        event.stopPropagation();
        fetch('{{ route("dashboard.reservation.notifications.mark_all_read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).then(() => {
            location.reload();
        }).catch(() => {});
    }
</script>

@include('layouts.partials.password-update-modal', ['passwordModalId' => 'passwordUpdateModal'])
