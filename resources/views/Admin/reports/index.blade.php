@extends('layouts.app')

@section('title', __('dashboard.reports'))

@section('content')
    <div class="container my-5 bg-white p-3" style="border-radius:15px;">
        <div class="mb-4">
            <h2>{{ __('dashboard.reports') }}</h2>
            <p class="text-muted">
                {{ __('dashboard.all_statistical_reports_needed_to_collect_information_about_your_property_are_listed_here_and_grouped_by_purpose') }}
            </p>
        </div>

        <div class="row g-4">
            {{-- Financial Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i> {{ __('dashboard.financial_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.financial_transactions') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.financial_transactions_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.daily_transactions') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.daily_transactions_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.trial_balance') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.trial_balance_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.tax') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.taxes_and_fees_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.reservation_balances') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.reservation_balances_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Voucher Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i> {{ __('dashboard.voucher_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.receipt_vouchers') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.receipt_vouchers_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.payment_vouchers') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.payments_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.invoices') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.invoices_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.credit_notes') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.credit_notes_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.promissory_notes') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.promissory_notes_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.drop_cash') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.drop_cash_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Revenue Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-chart-bar me-2 text-warning"></i> {{ __('dashboard.revenue_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.guest_ledger') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.guest_ledger_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.city_ledger') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.city_ledger_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.revenue_by_source') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.revenue_by_source_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.reservation_revenue') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.reservation_revenue_reports') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Reservation Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-calendar-alt me-2 text-info"></i> {{ __('dashboard.reservation_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.reservation_summary') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.reservation_summary_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.reservation_details') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.reservation_details_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.expected_arrivals') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.expected_arrivals_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.expected_departures') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.expected_departures_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Night Audit Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-moon me-2 text-dark"></i> {{ __('dashboard.night_audit_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.night_audit_summary') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.night_audit_summary_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.night_audit_history') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.night_audit_history_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Housekeeping Reports --}}
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center mb-3">
                            <i class="fas fa-broom me-2 text-secondary"></i> {{ __('dashboard.housekeeping_reports') }}
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.housekeeping_status') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.housekeeping_status_report') }}
                                </a>
                            </li>
                            <li class="py-1">
                                <a href="{{ route('dashboard.reports.occupancy') }}" class="text-primary text-decoration-none">
                                    <i class="fas fa-chevron-right me-2 small"></i>{{ __('dashboard.occupancy_report') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
