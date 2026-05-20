<header class="super-admin-header">
    <div class="super-admin-header__card">
        <div>
            <div class="super-admin-header__eyebrow">Administration</div>
            <h1 class="super-admin-header__title">
                @yield('page_title', 'Super Admin Panel')
            </h1>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-plus me-1"></i>
                New Tenant
            </a>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                    <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passwordUpdateModal">
                            <i class="fas fa-key me-2"></i>
                            {{ __('dashboard.update_password') }}
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

@include('layouts.partials.password-update-modal', ['passwordModalId' => 'passwordUpdateModal'])
