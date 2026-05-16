@php
    $supportUnreadCount = app(\App\Support\SupportTicketUnreadCounter::class)->forSuperAdmin();
@endphp

<aside class="super-admin-sidebar" id="superAdminSidebar">
    <div class="super-admin-brand">
        <div class="super-admin-brand__logo">SA</div>
        <div>
            <p class="super-admin-brand__title">Reservation SaaS</p>
            <p class="super-admin-brand__subtitle">Super Admin Control</p>
        </div>
    </div>

    <nav class="super-admin-nav">
        <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'is-active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('super-admin.tenants.index') }}" class="{{ request()->routeIs('super-admin.tenants.*') ? 'is-active' : '' }}">
            <i class="fas fa-building"></i>
            <span>Tenants</span>
        </a>

        <a href="{{ route('super-admin.plans.index') }}" class="{{ request()->routeIs('super-admin.plans.*') ? 'is-active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>Plans</span>
        </a>

        <a href="{{ route('super-admin.analytics.index') }}" class="{{ request()->routeIs('super-admin.analytics.*') ? 'is-active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </a>

        <a href="{{ route('super-admin.support.index') }}" class="{{ request()->routeIs('super-admin.support.*') ? 'is-active' : '' }}">
            <i class="fas fa-headset"></i>
            <span>Support</span>
            @if($supportUnreadCount > 0)
                <span class="support-unread-count badge bg-danger rounded-pill ms-auto" data-support-unread-count="{{ $supportUnreadCount }}">
                    {{ $supportUnreadCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('super-admin.activity.index') }}" class="{{ request()->routeIs('super-admin.activity.*') ? 'is-active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Activity</span>
        </a>
    </nav>
</aside>
