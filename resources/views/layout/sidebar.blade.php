<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">

            @php
                $setting = App\Models\GeneralSetting::first();
            @endphp
            @if (!empty($setting))
                <a href="#"> <img
                        src="{{ $setting->logo_path ? asset($setting->logo_path) : 'https://randomuser.me/api/portraits/men/75.jpg' }}"
                        alt="Logo" class="logo" width="40">

                    <span class="logo-name">{{ $setting->hrm_name }}</span>
                @else
                    <span class="logo-name">{{ __('dashboard.name') }}</span>
            @endif
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">{{ __('dashboard.main') }}</li>
            <li class="dropdown {{ request()->routeIs('dashboard.program') ? 'active' : '' }}">
                <a href="{{ route('dashboard.program') }}" class="nav-link">
                    <i data-feather="book-open"></i>
                    <span>{{ __('dashboard.program') }}</span>
                </a>
            </li>
            @can('manage_dashboard')
                <li class="dropdown {{ request()->routeIs('dashboard.branch.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.branch.dashboard') }}" class="nav-link">
                        <i data-feather="monitor"></i>
                        <span>{{ __('dashboard.dashboard') }}</span>
                    </a>
                </li>
            @endcan




            @can('manage_employee')
                <li class="menu-header">{{ __('dashboard.employee_management') }}</li>
                <li class="dropdown {{ request()->is('dashboard/employee*') ? 'active' : '' }}">

                    <a href="#"
                        class="menu-toggle nav-link has-dropdown {{ request()->is('dashboard/employee*') ? 'text-secondary fw-bold' : '' }}">
                        <i data-feather="users"
                            class="{{ request()->is('dashboard/employee*') ? 'text-secondary' : '' }}"></i>
                        <span>{{ __('dashboard.employee_management') }}</span>
                    </a>




                    <ul class="dropdown-menu" style="{{ request()->is('dashboard/employee*') ? 'display: block;' : '' }}">
                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.index') }}">
                                {{ __('dashboard.all_employees') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('dashboard.shift.index') }}">
                                {{ __('dashboard.shifts') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.card') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.card') }}">
                                {{ __('dashboard.id_card_generator') }}
                            </a>
                        </li>

                    </ul>
                </li>
            @endcan

            @can('manage_attendance')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="clock"></i><span>{{ __('dashboard.attendance_and_leave') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ request()->is('dashboard/attendance/dashboard*') || request()->is('dashboard/attendance*') || request()->is('dashboard/leaves*') || request()->is('dashboard/overtime*') || request()->is('dashboard/attendance/absent*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.attendance.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.attendance.dashboard') }}">
                                {{ __('dashboard.attendance_dashboard') }}
                            </a>
                        </li>



                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.attendance.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.attendance.index') }}">
                                {{ __('dashboard.mark_attendance') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.leave.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.leave.index') }}">
                                {{ __('dashboard.leaves') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.overtime.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.overtime.index') }}">
                                {{ __('dashboard.overtime') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.absence.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.absence.index') }}">
                                {{ __('dashboard.absence') }}
                            </a>
                        </li>
                    </ul>

                </li>
            @endcan

            <li class="dropdown">
                @can('manage_payroll')
                <li class="menu-header">{{ __('dashboard.finance') }}</li>
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="dollar-sign"></i><span>{{ __('dashboard.payroll_and_salary') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ request()->is('dashboard/payroll/dashboard*') || request()->is('dashboard/payroll*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.payroll.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.payroll.dashboard') }}">
                                {{ __('dashboard.payroll_dashboard') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.payroll.payslip') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.payroll.payslip') }}">
                                {{ __('dashboard.payroll_list') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link" href="{{ route('dashboard.payroll.salary') }}">
                                {{ __('dashboard.salary_management') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.payroll.salary.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.payroll.salary.index') }}">
                                {{ __('dashboard.salary_history') }}
                            </a>
                        </li>
                    </ul>

                </li>
            @endcan
            @can('manage_finance')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="credit-card"></i><span>{{ __('dashboard.finance_and_account') }}</span></a>
                    <ul class="dropdown-menu" style="{{ request()->is('dashboard/finance*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.index') }}">
                                {{ __('dashboard.financial_dashboard') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.income.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.income.index') }}">
                                {{ __('dashboard.income') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.transaction.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.transaction.index') }}">
                                {{ __('dashboard.transactions') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.budget.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.budget.index') }}">
                                {{ __('dashboard.budget_management') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.expense.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.expense.index') }}">
                                {{ __('dashboard.administrative_expenses') }}
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('dashboard.finance.commission.index') }}">
                                {{ __('dashboard.commission_report') }}
                            </a>
                        </li>
                        @if (Auth::user()->hasRole('super_admin'))
                            <li>
                                <a class="nav-link" href="{{ route('dashboard.finance.partner.index') }}">
                                    {{ __('dashboard.company_partners') }}
                                </a>
                            </li>



                            <li>
                                <a class="nav-link" href="{{ route('dashboard.finance.partner.report') }}">
                                    {{ __('dashboard.partner_reports') }}
                                </a>
                            </li>
                        @else
                        @endif
                    </ul>

                </li>
            @endcan


            <li class="dropdown">
                @can('manage_documents')
                <li class="menu-header">{{ __('dashboard.documents_and_branch') }}</li>
                <li class="dropdown">

                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="file"></i><span>{{ __('dashboard.document_management') }}</span></a>
                    <ul class="dropdown-menu" style="{{ request()->is('dashboard/document*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.document.employee.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.document.employee.index') }}">
                                {{ __('dashboard.employee_documents') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.document.company.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.document.company.index') }}">
                                {{ __('dashboard.company_documents') }}
                            </a>
                        </li>



                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.document.expired.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.document.expired.index') }}">
                                {{ __('dashboard.expiration_alert') }}
                            </a>
                        </li>

                    </ul>

                </li>
            @endcan

            @can('manage_branch')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown">
                        <i data-feather="map-pin"></i>
                        <span>{{ __('dashboard.company_management') }}</span>
                    </a>

                    <ul class="dropdown-menu" style="{{ request()->is('dashboard/branch*') ? 'display: block;' : '' }}">

                        <!-- First Nested Dropdown: Branch & Company Info -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.branch_company_info') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link {{ request()->routeIs('dashboard.branch.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                        href="{{ route('dashboard.branch.dashboard') }}">
                                        {{ __('dashboard.branch_dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.index') }}">
                                        {{ __('dashboard.company_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.brand.index') }}">
                                        {{ __('dashboard.brands_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link {{ request()->routeIs('dashboard.branch.index') ? 'active text-secondary fw-bold' : '' }}"
                                        href="{{ route('dashboard.branch.index') }}">
                                        {{ __('dashboard.branch_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link {{ request()->routeIs('dashboard.branch.department.index') ? 'active text-secondary fw-bold' : '' }}"
                                        href="{{ route('dashboard.branch.department.index') }}">
                                        {{ __('dashboard.department_management') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.report') }}">
                                        {{ __('dashboard.company_report') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Second Nested Dropdown: Company Letters -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.company_letters') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.letter.index') }}">
                                        {{ __('dashboard.company_letter') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.letter.setting.index') }}">
                                        {{ __('dashboard.letter_setting') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Third Nested Dropdown: Marketing -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.marketing') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.agent.index') }}">
                                        {{ __('dashboard.create_agent') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.quotation.index') }}">
                                        {{ __('dashboard.marketing_quotations') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.commission.index') }}">
                                        {{ __('dashboard.marketing_commission') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Fourth Nested Dropdown: Subscription & Platform -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.subscription_management') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.platform.index') }}">
                                        {{ __('dashboard.subscription_platforms') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.subscription.index') }}">
                                        {{ __('dashboard.subscription') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.revenue.index') }}">
                                        {{ __('dashboard.subscription_revenue') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Fourth Nested Dropdown: Subscription & Platform -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.client_management') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.client.index') }}">
                                        {{ __('dashboard.client_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.contract.index') }}">
                                        {{ __('dashboard.contracts') }}
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <!-- Fifth Nested Dropdown: vehicles & drivers -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.vehicle_management') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.vehicle.index') }}">
                                        {{ __('dashboard.vehicle_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.driver.index') }}">
                                        {{ __('dashboard.driver') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.accident.index') }}">
                                        {{ __('dashboard.accidents') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- sixth Nested Dropdown: vehicles & drivers -->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown">
                                {{ __('dashboard.project_management') }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.project.index') }}">
                                        {{ __('dashboard.project_list') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.executive.index') }}">
                                        {{ __('dashboard.project_executives') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.tracker.index') }}">
                                        {{ __('dashboard.project_trackers') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ route('dashboard.company.expense.index') }}">
                                        {{ __('dashboard.project_expenses') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>
            @endcan
            @can('manage_notification')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="bell"></i><span>{{ __('dashboard.notification_and_alerts') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ request()->is('dashboard/notification*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.notification.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.notification.dashboard') }}">
                                {{ __('dashboard.sms/email_alert') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.notification.custom.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.notification.custom.index') }}">
                                {{ __('dashboard.custom_notification') }}
                            </a>
                        </li>

                    </ul>

                </li>
            @endcan

            <li class="dropdown">
                @can('manage_warehouse')
                <li class="menu-header">{{ __('dashboard.warehouse_management') }}</li>

                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="dollar-sign"></i><span>{{ __('dashboard.warehouses') }}</span></a>
                    @if (Auth::user()->hasRole('super_admin') && $pendingStockRequestsCount > 0)
                        <span class="badge badge-danger" style="margin-left:150px;margin-top:-80px;">
                            {{ $pendingStockRequestsCount }}
                        </span>
                    @endif
                    <ul class="dropdown-menu" style="">


                        @can('manage_warehouse')
                            <li>
                                <a class="nav-link" href="{{ route('dashboard.warehouse.index') }}"
                                    style="margin-top:-10px;">
                                    {{ __('dashboard.warehouse') }}
                                </a>
                            </li>

                            <li>
                                <a class="nav-link" href="{{ route('dashboard.room.index') }}">
                                    {{ __('dashboard.create_section') }}
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('dashboard.category.index') }}">
                                    {{ __('dashboard.type') }}
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{ route('dashboard.product.index') }}">
                                    {{ __('dashboard.classify') }}
                                </a>
                            </li>


                            <li>
                                <a class="nav-link" href="{{ route('dashboard.inventory.index') }}">
                                    {{ __('dashboard.inventory') }}
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a class="nav-link" href="{{ route('dashboard.requests.index') }}">
                                {{ __('dashboard.request') }}

                            </a>
                            @if (Auth::user()->hasRole('super_admin') && $pendingStockRequestsCount > 0)
                                <span class="badge badge-danger" style="margin-left:110px;margin-top:-80px;">
                                    {{ $pendingStockRequestsCount }}
                                </span>
                            @endif

                        </li>
                    </ul>

                </li>
            @endcan

            @can('manage_dashboard')
            <li class="dropdown">
                <li class="menu-header">{{ __('dashboard.chatting_system') }}</li>
            <li class="dropdown">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i
                        data-feather="message-circle"></i><span>{{ __('dashboard.chatting') }}</span></a>
                <ul class="dropdown-menu" style="">

                    <li>
                        <a class="nav-link" href="{{ url('/chatify') }}">
                            {{ __('dashboard.start_chat') }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="{{ route('dashboard.meetings.index') }}">
                            {{ __('dashboard.meetings') }}
                        </a>
                    </li>
                </ul>

            </li>
            @endcan

            <li class="dropdown">
                @can('manage_setting')
                <li class="menu-header">{{ __('dashboard.setting_and_reports') }}</li>
                <li class="dropdown">

                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="settings"></i><span>{{ __('dashboard.setting_and_configuration') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ request()->is('dashboard/setting*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.setting.general.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.setting.general.index') }}">
                                {{ __('dashboard.general_setting') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.setting.role.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.setting.role.index') }}">
                                {{ __('dashboard.role_and_permissions') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.setting.user.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.setting.user.index') }}">
                                {{ __('dashboard.user_access_management') }}
                            </a>
                        </li>


                    </ul>

                </li>
            @endcan

            @can('manage_reports')
                <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            data-feather="bar-chart-2"></i><span>{{ __('dashboard.report_and_analytics') }}</span></a>
                    <ul class="dropdown-menu"
                        style="{{ request()->is('dashboard/employee/payroll') || request()->is('dashboard/finance*') || request()->is('dashboard/employee/attendance*') ? 'display: block;' : '' }}">

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.payroll.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.payroll.dashboard') }}">
                                {{ __('dashboard.payroll_reports') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.finance.index') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.finance.report') }}">
                                {{ __('dashboard.financial_reports') }}
                            </a>
                        </li>

                        <li>
                            <a class="nav-link {{ request()->routeIs('dashboard.employee.attendance.dashboard') ? 'active text-secondary fw-bold' : '' }}"
                                href="{{ route('dashboard.employee.attendance.dashboard') }}">
                                {{ __('dashboard.attendance_report') }}
                            </a>
                        </li>
                        {{-- <li>
                            <a class="nav-link " href="{{ route('dashboard.finance.report') }}">
                                {{ __('dashboard.finance_report') }}
                            </a>
                        </li> --}}

                    </ul>

                </li>
            @endcan

            {{-- Reservation System Module --}}
            <li class="menu-header">{{ __('Reservation') }}</li>
            <li class="dropdown {{ request()->is('reservation*') ? 'active' : '' }}">
                <a href="{{ url('/reservation/dashboard') }}" class="nav-link">
                    <i data-feather="calendar"></i>
                    <span>{{ __('Reservation Dashboard') }}</span>
                </a>
            </li>

            </li>
            </li>
            </li>
        </ul>
    </aside>
</div>
