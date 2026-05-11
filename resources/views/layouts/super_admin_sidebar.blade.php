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
    </nav>
</aside>
