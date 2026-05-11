@extends('layout.master')
@section('title', 'Dashboard | Program')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="row">
                <!-- Dashboard -->

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 custom-width">
                    <a href="{{ route('dashboard.index') }}" class="text-decoration-none">
                        <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                            <i data-feather="monitor" style="width:50px; height:50px;" class="mb-2"></i>
                            <h5 class="font-16 text-center mb-0">{{ __('dashboard.title') }}</h5>
                        </div>
                    </a>
                </div>
                @can('manage_employee')
                    <!-- Employee -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.employee.index') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="user" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.employees') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_attendance')
                    <!-- Attendance -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.employee.attendance.dashboard') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="clock" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.attendance') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_payroll')
                    <!-- Payroll -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.employee.payroll.dashboard') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="credit-card" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.payroll') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

            </div>
        </section>
        <section class="section">
            <div class="row">
                @can('manage_finance')
                    <!-- Finance -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.finance.index') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="dollar-sign" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.finance') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_documents')
                    <!-- Documents -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.document.employee.index') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="file-text" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.documents') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_branch')
                    <!-- Branch -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.branch.dashboard') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="map-pin" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.branch') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_notification')
                    <!-- Notifications -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.notification.dashboard') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="bell" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.notifications') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

            </div>
        </section>
        <section class="section">
            <div class="row">
                @can('manage_setting')
                    <!-- Setting -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.setting.general.index') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="settings" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.settings') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

                @can('manage_reports')
                    <!-- Reports -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <a href="{{ route('dashboard.employee.payroll.dashboard') }}" class="text-decoration-none">
                            <div class="card h-100 d-flex align-items-center justify-content-center custom-card-height">
                                <i data-feather="bar-chart-2" style="width:50px; height:50px;" class="mb-2"></i>
                                <h5 class="font-16 text-center mb-0">{{ __('dashboard.reports') }}</h5>
                            </div>
                        </a>
                    </div>
                @endcan

            </div>
        </section>
    </div>


@endsection
