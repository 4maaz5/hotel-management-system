<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payslip – {{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</title>
    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: #fff;
            font-size: 13px;
        }

        .payslip-container {
            width: 100%;
            max-width: 700px;
            /*  Fit safely within A4 printable width */
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 25px 30px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            width: 70px;
            height: auto;
            margin-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            color: #007bff;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
            color: #555;
        }

        .title-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .title-section h3 {
            margin: 0;
            font-size: 18px;
        }

        .title-section p {
            margin: 4px 0;
            font-size: 13px;
            color: #777;
        }

        .employee-details,
        .salary-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .employee-details th,
        .employee-details td,
        .salary-details th,
        .salary-details td {
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 12px;
            text-align: left;
        }

        .employee-details th {
            width: 30%;
            background: #f7f7f7;
        }

        .salary-sections {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .salary-block {
            flex: 1;
        }

        .salary-block h4 {
            font-size: 15px;
            color: #007bff;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .salary-block table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-block table th,
        .salary-block table td {
            padding: 7px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .salary-block table th {
            background: #f7f7f7;
        }

        .salary-block .total-row {
            font-weight: bold;
            background: #f0f8ff;
        }

        .net-salary {
            margin-top: 25px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }

        .net-salary .in-words {
            font-size: 12px;
            color: #555;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>

</head>

<body>
    <div class="payslip-container">

        <!-- Header -->
        <div class="header">
            <img src="{{ !empty($setting) && $setting->logo_path
                ? public_path($setting->logo_path)
                : 'https://upload.wikimedia.org/wikipedia/commons/a/ab/Logo_TV_2022.svg' }}"
                alt="logo" class="logo" width="80">

            <h2>{{ $setting->hrm_name ?? '-' }}</h2>
            <p>{{ __('dashboard.email') }}: {{ $setting->email ?? '-' }}</p>
            <p>{{ __('dashboard.phone') }}: {{ $setting->phone ?? '-' }}</p>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <h3>{{ __('dashboard.payslip') }}</h3>
            <p>{{ __('dashboard.payslip_for_the_month_of') }}
                {{ \Carbon\Carbon::createFromFormat('Y-m', $payslip->month)->format('F, Y') }}
            </p>
        </div>

        <!-- Payslip Info (ID / Month) -->
        <table class="salary-details">
            <tr>
                <th>{{ __('dashboard.payslip') }} #</th>
                <td>{{ $payslip->id }}</td>
                <th>{{ __('dashboard.salary_month') }}</th>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payslip->month)->format('F, Y') }}</td>
            </tr>
        </table>

        <!-- Employee Details -->
        <table class="employee-details">
            <tr>
                <th>{{ __('dashboard.company_name') }}</th>
                <td>{{ $payslip->employee->company->name }} </td>
                <th>{{ __('dashboard.brand_name') }}</th>
                <td>{{ $payslip->employee->brand->name }}</td>
            </tr>
            <tr>
                <th>{{ __('dashboard.branch_name') }}</th>
                <td>{{ $payslip->employee->branch->name }}</td>
                <th>{{ __('dashboard.department_name') }}</th>
                <td>{{ $payslip->employee->department->name }}</td>
            </tr>
            <tr>
                <th>{{ __('dashboard.employee_name') }}</th>
                <td>{{ $payslip->employee->first_name }} {{ $payslip->employee->last_name }}</td>
                <th>{{ __('dashboard.employee_id') }}</th>
                <td>{{ $payslip->employee->employee_id }}</td>
            </tr>
            <tr>
                <th>{{ __('dashboard.designation') }}</th>
                <td>{{ $payslip->employee->designation }}</td>
                <th>{{ __('dashboard.join_date') }}</th>
                <td>{{ \Carbon\Carbon::parse($payslip->employee->join_date)->format('d M Y') }}</td>
            </tr>
        </table>

        <!-- Earnings & Deductions Section -->
        <div class="salary-sections">
            <!-- Earnings -->
            <div class="salary-block">
                <h4>{{ __('dashboard.earnings') }}</h4>
                <table>
                    <tr>
                        <th>{{ __('dashboard.description') }}</th>
                        <th>{{ __('dashboard.amount') }} (SAR)</th>
                    </tr>
                    <tr>
                        <td>{{ __('dashboard.basic_salary') }}</td>
                        <td>{{ number_format($payslip->basic_salary, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('dashboard.total_commission') }}</td>
                        <td>{{ number_format($payslip->commission_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('dashboard.allowances') }}</td>
                        <td>{{ number_format($payslip->allowance ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('dashboard.deductions') }}</td>
                        <td>{{ number_format($payslip->deductions ?? 0, 2) }}</td>
                    </tr>


                    <tr class="total-row">
                        <td>{{ __('dashboard.total_earnings') }}</td>
                        <td>{{ number_format($payslip->total_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </div>


        </div>

        <!-- Net Salary -->
        <div class="net-salary">
            {{ __('dashboard.net_salary') }}: {{ number_format($payslip->total_amount, 2) }} SAR<br>
            <span class="in-words">
                ({{ \App\Helpers\NumberToWords::convert($payslip->total_amount) }} only.)
            </span>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ __('dashboard.this_is_a_system') }} </div>

    </div>
</body>

</html>
