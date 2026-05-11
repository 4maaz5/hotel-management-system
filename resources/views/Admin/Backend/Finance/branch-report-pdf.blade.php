<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Branch Finance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2,
        h4,
        h5 {
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .card {
            border-radius: 8px;
            padding: 10px;
            color: #fff;
            margin-bottom: 10px;
        }

        .bg-success {
            background-color: #28a745;
        }

        .bg-danger {
            background-color: #dc3545;
        }

        .bg-primary {
            background-color: #007bff;
        }

        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        .summary-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .summary-table td {
            border: none;
            padding: 10px;
            font-weight: bold;
        }
    </style>
    <script>
        // Trigger print dialog on page load
        window.onload = function() {
            window.print();
        };
    </script>
</head>

<body>

    <div class="card-body" id="printable-report">
        {{-- @if ($selectedCompany) --}}
        @php
            $companyName = $company->legal_name ?? '';
            $companyAddress = $company->city ?? 'N/A';
            $companyCR = $company->cr_number ?? 'N/A';
            $companyLogo = $company->logo ?? null;
        @endphp

        <!-- Report Header -->
        <div
            style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); padding: 20px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">

            <!-- Left Side - Company Details -->
            <div
                style="flex: 1; padding-right: 20px; border-left: 4px solid #667eea; background: #f8f9fc; border-radius: 10px; padding: 20px;">
                <div style="margin-bottom: 8px;">
                    <span
                        style="font-weight:bold; min-width:140px; display:inline-block;">{{ __('dashboard.company_name') }}:</span>
                    <span>{{ $companyName }}</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <span
                        style="font-weight:bold; min-width:140px; display:inline-block;">{{ __('dashboard.address') }}:</span>
                    <span>{{ $companyAddress }}</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <span
                        style="font-weight:bold; min-width:140px; display:inline-block;">{{ __('dashboard.cr_number') }}:</span>
                    <span>{{ $companyCR }}</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <span
                        style="font-weight:bold; min-width:140px; display:inline-block;">{{ __('dashboard.print_date') }}:</span>
                    <span>{{ now()->format('d M Y, h:i A') }}</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <span
                        style="font-weight:bold; min-width:140px; display:inline-block;">{{ __('dashboard.generated_by') }}:</span>
                    <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </div>

            <!-- Right Side - Company Logo -->
            <div style="width: 120px; height: 120px; text-align:center;margin-left:470px;margin-top:-100px;">
                @if ($companyLogo)
                    <img src="{{ public_path('storage/' . $companyLogo) }}"
                        style="max-width: 120px; max-height: 120px; object-fit: contain;">
                @else
                    <div
                        style="width: 120px; height: 120px; border:2px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; border-radius:10px;">
                        <span>No Logo</span>
                    </div>
                @endif
            </div>

        </div>


        <!-- Brands Section -->
        <div class="mb-4">
            <h5 class="font-weight-bold mb-3 pb-2" style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-tag mr-2" style="color: #667eea;"></i>Brands
            </h5>
            @php
                $companyBrands = $company->brands ?? collect();
            @endphp

            @if ($companyBrands->isNotEmpty())
                <div class="row">
                    @foreach ($companyBrands as $brand)
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
            <h5 class="font-weight-bold mb-3 pb-2" style="color: #2d3748; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-building mr-2" style="color: #764ba2;"></i>{{ __('dashboard.branches') }}
            </h5>
            @php
                $companyBranches = $company->branches ?? collect();
            @endphp

            @if ($companyBranches->isNotEmpty())
                <div class="row" id="branch-list">
                    @foreach ($companyBranches as $branch)
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
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>No branches found for this company.
                </div>
            @endif


            <!-- Branch report will be rendered here -->
            <div id="branch-report" class="mt-4"></div>

        </div>

    </div>

    <h2 style="text-align:center; margin-bottom:20px;">
        {{ __('dashboard.finance_report_branch') }}:
        {{ $employees->first() ? $employees->first()->branch->name : 'N/A' }}
    </h2>

    <!-- Employee Payrolls -->
    <h5>{{ __('dashboard.employee_payrolls') }}</h5>
    <table>
        <thead>
            <tr>
                <th>{{ __('dashboard.employee_name') }}</th>
                <th>{{ __('dashboard.total_salary') }}</th>
                <th>{{ __('dashboard.net_salary') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $emp)
                <tr>
                    <td>{{ $emp->first_name . ' ' . $emp->last_name }}</td>
                    <td>{{ number_format($emp->payroll->sum('total_salary'), 2) }}</td>
                    <td>{{ number_format($emp->payroll->sum('net_salary'), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Incomes -->
    <h5>{{ __('dashboard.incomes') }}</h5>
    <table>
        <thead>
            <tr>
                <th>{{ __('dashboard.type') }}</th>
                <th>{{ __('dashboard.amount') }}</th>
                <th>{{ __('dashboard.payment_type') }}</th>
                <th>{{ __('dashboard.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incomes as $inc)
                <tr>
                    <td>{{ $inc->type }}</td>
                    <td>{{ number_format($inc->amount, 2) }}</td>
                    <td>{{ $inc->payment_type ?? '--' }}</td>
                    <td>{{ $inc->income_date }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8f9fa;">
                <td>{{ __('dashboard.total_income') }}</td>
                <td>{{ number_format($incomes->sum('amount'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Administrative Expenses -->
    <h5>{{ __('dashboard.administrative_expenses') }}</h5>
    <table>
        <thead>
            <tr>
                <th>{{ __('dashboard.item_name') }}</th>
                <th>{{ __('dashboard.invoice') }} #</th>
                <th>{{ __('dashboard.quantity') }}</th>
                <th>{{ __('dashboard.amount') }}</th>
                <th>{{ __('dashboard.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $exp)
                <tr>
                    <td>{{ $exp->item_name }}</td>
                    <td>{{ $exp->invoice_number }}</td>
                    <td>{{ $exp->quantity }}</td>
                    <td>{{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->expense_date }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#fafafa;">
                <td colspan="3">{{ __('dashboard.total_expenses') }}</td>
                <td>{{ number_format($expenses->sum('amount'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @php
        $totalSalaries = $employees->sum(fn($emp) => $emp->payroll->sum('net_salary'));
        $totalIncomes = $incomes->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalIncomes - ($totalExpenses + $totalSalaries);
    @endphp

    <h4 class="mt-4" style="margin-top:80px;">{{ __('dashboard.branch_financial_summary') }}</h4>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">

        <div class="card bg-success shadow-sm" style="flex:1;">
            <h6>{{ __('dashboard.total_income') }}</h6>
            <h4>{{ number_format($totalIncomes, 2) }}</h4>
        </div>

        <div class="card bg-danger shadow-sm" style="flex:1;">
            <h6>{{ __('dashboard.total_expenses') }}</h6>
            <h4>{{ number_format($totalExpenses, 2) }}</h4>
        </div>

        <div class="card bg-primary shadow-sm" style="flex:1;">
            <h6>{{ __('dashboard.total_salaries_paid') }}</h6>
            <h4>{{ number_format($totalSalaries, 2) }}</h4>
        </div>

        <div class="card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} shadow-sm" style="flex:1;">
            <h6>{{ __('dashboard.net_profit') }}</h6>
            <h4>{{ number_format($netProfit, 2) }}</h4>
        </div>

    </div>

</body>

</html>
