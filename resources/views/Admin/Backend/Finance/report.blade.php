@extends('layout.master')
@section('title', 'Finance Report')
@section('main')
    <div class="main-content">

        <section class="section">
            <div class="row ">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.total_income') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalIncome }} SAR</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/average1.png') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.admin_expense') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalExpenses }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/pending1.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.payroll_cost') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $payrollCost }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/salary1.avif') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.pending_payrolls') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $pendingTransactions }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/percent1.png') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container-fluid">
            <div class="card shadow-sm" style="border: none; border-radius: 15px;">
                <div class="card-header bg-white border-bottom" style="padding: 1.5rem; border-radius: 15px 15px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold">{{ __('dashboard.finance_report') }}</h4>

                        <!-- Company Filter -->
                        <form method="GET" class="form-inline">
                            <select name="company_id" class="form-control mr-2" style="min-width: 200px;">
                                <option value="">{{ __('dashboard.select_company') }}</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ $selectedCompany == $company->id ? 'selected' : '' }}>
                                        {{ $company->legal_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter mr-1"></i>{{ __('dashboard.filter') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body" id="printable-report">
                    @if ($selectedCompany)
                        @php
                            $company = $companies->where('id', $selectedCompany)->first();
                            $companyName = $company->legal_name ?? '';
                            $companyAddress = $company->city ?? 'N/A';
                            $companyCR = $company->cr_number ?? 'N/A';
                            $companyLogo = $company->logo ?? null;
                        @endphp

                        <!-- Report Header -->
                        <div class="mb-4 pb-4 border-bottom"
                            style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); padding: 20px; border-radius: 10px;">
                            <h3 class="text-center mb-4 font-weight-bold" style="color: #2d3748;">
                                {{ __('dashboard.finance_report') }}</h3>

                            <div class="row ">
                                <!-- Left Side - Company Details -->
                                <div class="col-md-8">
                                    <div
                                        style="background: #f8f9fc; padding: 20px; border-radius: 10px; border-left: 4px solid #667eea;">
                                        <div class="d-flex mb-2">
                                            <span class="font-weight-bold"
                                                style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.company_name') }}:</span>
                                            <span style="color: #4a5568; font-size: 14px;">{{ $companyName }}</span>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <span class="font-weight-bold"
                                                style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.address') }}:</span>
                                            <span style="color: #4a5568; font-size: 14px;">{{ $companyAddress }}</span>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <span class="font-weight-bold"
                                                style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.cr_number') }}:</span>
                                            <span style="color: #4a5568; font-size: 14px;">{{ $companyCR }}</span>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <span class="font-weight-bold"
                                                style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.print_date') }}:</span>
                                            <span
                                                style="color: #4a5568; font-size: 14px;">{{ now()->format('d M Y, h:i A') }}</span>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <span class="font-weight-bold"
                                                style="color: #2d3748; min-width: 140px; font-size: 14px;">{{ __('dashboard.generated_by') }}:</span>
                                            <span
                                                style="color: #4a5568; font-size: 14px;">{{ Auth::user()->name ?? 'Admin' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side - Company Logo -->
                                <div class="col-md-4 text-right">
                                    @if ($companyLogo)
                                        <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}"
                                            style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center ml-auto"
                                            style="width: 150px; height: 150px; background: #f7fafc; border: 2px dashed #cbd5e1; border-radius: 10px;">
                                            <span class="text-muted">No Logo</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Brands Section -->
                        <div class="mb-4">
                            <h5 class="font-weight-bold mb-3 pb-2"
                                style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                                <i class="fas fa-tag mr-2" style="color: #667eea;"></i>{{ __('dashboard.brand') }}
                            </h5>
                            @if ($brands->isNotEmpty())
                                <div class="row">
                                    @foreach ($brands as $brand)
                                        <div class="col-md-4 mb-3">
                                            <div
                                                style="background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 12px 15px; transition: all 0.3s ease;">
                                                <i class="fas fa-circle mr-2" style="color: #667eea; font-size: 8px;"></i>
                                                <span
                                                    style="color: #2d3748; font-weight: 600; font-size: 14px;">{{ $brand->name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{-- <i class="fas fa-info-circle mr-2"></i>No brands found for this company. --}}
                                </div>
                            @endif
                        </div>

                        <!-- Branches Section -->
                        <div class="mb-4">
                            <h5 class="font-weight-bold mb-3 pb-2"
                                style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                                <i class="fas fa-building mr-2"
                                    style="color: #764ba2;"></i>{{ __('dashboard.branches') }}
                            </h5>
                            @if ($branches->isNotEmpty())
                                <div class="row" id="branch-list">
                                    @foreach ($branches as $branch)
                                        <div class="col-md-4 mb-3">
                                            <div class="branch-box" data-branch="{{ $branch->id }}"
                                                style="background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 12px 15px; transition: all 0.3s ease; cursor:pointer;">
                                                <i class="fas fa-circle mr-2" style="color: #764ba2; font-size: 8px;"></i>
                                                <span style="color: #2d3748; font-weight: 600; font-size: 14px;">
                                                    {{ $branch->name }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-3">
                                    <input type="month" class="form-control" id="selected-month"
                                        value="{{ $month ?? date('Y-m') }}">
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>{{ __('dashboard.no_branches_found') }}
                                </div>
                            @endif

                            <!-- Branch report will be rendered here -->
                            <div id="branch-report" class="mt-4"></div>


                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line" style="font-size: 4rem; color: #cbd5e1;"></i>
                            <p class="mt-3 text-muted">{{ __('dashboard.please_select') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // document.addEventListener('DOMContentLoaded', function() {

        //     document.querySelectorAll('.branch-box').forEach(function(box) {

        //         box.addEventListener('click', function() {

        //             let branchId = this.dataset.branch;
        //             let brandId = null; // keep if needed

        //             // Make sure month is in YYYY-MM format
        //             let month = "{{ $month ?? date('Y-m') }}";

        //             // Build URL WITHOUT year (since payrolls table has no year column)
        //             let url =
        //                 `{{ route('finance.branch.data') }}?branch_id=${branchId}&brand_id=${brandId}&month=${month}`;

        //             fetch(url)
        //                 .then(res => res.text())
        //                 .then(html => {
        //                     document.getElementById('branch-report').innerHTML = html;
        //                 });
        //         });

        //     });

        // });

        document.addEventListener('DOMContentLoaded', function() {

            const monthInput = document.getElementById('selected-month');

            // When a branch is clicked
            document.querySelectorAll('.branch-box').forEach(function(box) {

                box.addEventListener('click', function() {

                    let branchId = this.dataset.branch;
                    let brandId = null; // keep if needed

                    // Get selected month dynamically from input
                    let month = monthInput.value; // YYYY-MM format

                    // Build URL
                    let url =
                        `{{ route('finance.branch.data') }}?branch_id=${branchId}&brand_id=${brandId}&month=${month}`;

                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('branch-report').innerHTML = html;
                        });
                });

            });

            // Optional: if month changes, reload report for the first selected branch automatically
            monthInput.addEventListener('change', function() {
                const selectedBranch = document.querySelector(
                    '.branch-box.selected'); // if you add a selected class
                if (selectedBranch) {
                    selectedBranch.click(); // trigger click to reload report
                }
            });

        });
    </script>

@endsection
