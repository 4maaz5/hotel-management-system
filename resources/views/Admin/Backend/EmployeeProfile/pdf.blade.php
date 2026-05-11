<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Profile</title>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            color: #333;
        }

        .header {
            background: #0d6efd;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .section-title {
            background: #f1f5f9;
            color: #0d6efd;
            padding: 6px;
            font-weight: bold;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background: #0d6efd;
            color: #fff;
            padding: 6px;
            border: 1px solid #0d6efd;
            text-align: left;
        }

        td {
            padding: 6px;
            border: 1px solid #dee2e6;
        }

        .center {
            text-align: center;
        }

        .badge {
            background: #198754;
            color: #fff;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
        }

        .print-container {
            max-width: 900px;
            /* A4 friendly width */
            margin: 0 auto;
            /* center horizontally */
            padding: 20px;
        }

        /* Keep full width inside container */
        table {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="print-container">
        <div
            style="
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        padding: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    ">

            <!-- Left Side - Company Details -->
            <div style="flex: 1;">
                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.company_name') }}:
                    </strong>
                    {{ $employee->company->legal_name }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.brand_name') }}:
                    </strong>
                    {{ $employee->brand->name }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.country') }}:
                    </strong>
                    {{ $employee->company->country }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.cr_number') }}:
                    </strong>
                    {{ $employee->company->cr_number }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.print_date') }}:
                    </strong>
                    {{ now()->format('d M Y, h:i A') }}
                </div>

                <div style="margin-bottom: 8px;">
                    <strong style="min-width:140px; display:inline-block;">
                        {{ __('dashboard.generated_by') }}:
                    </strong>
                    {{ Auth::user()->name ?? 'Admin' }}
                </div>
            </div>

            <!-- Right Side - Company Logo -->
            <div
                style="
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.6);
            border-radius: 10px;
        ">
                @if ($employee->company->logo)
                    <img src="{{ asset('storage/' . $employee->company->logo) }}"
                        style="max-width: 120px; max-height: 120px; object-fit: contain;">
                @else
                    <span style="font-size:12px; color:#64748b;">No Logo</span>
                @endif
            </div>

        </div>


        <div class="header">
            {{ __('dashboard.employee_profile') }}
        </div>

        <!-- Employee Basic Info -->
        <table>
            <tr>
                <td width="25%"><strong>{{ __('dashboard.employee_name') }}</strong></td>
                <td width="25%">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td width="25%"><strong>{{ __('dashboard.employee_id') }}</strong></td>
                <td width="25%">{{ $employee->employee_id }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('dashboard.designation') }}</strong></td>
                <td>{{ $employee->designation }}</td>
                <td><strong>{{ __('dashboard.branch') }}</strong></td>
                <td>{{ $employee->branch->name ?? '-' }}</td>
            </tr>
        </table>

        <!-- Contact Information -->
        <div class="section-title">{{ __('dashboard.contact_information') }}</div>
        <table>
            <tr>
                <td>{{ __('dashboard.email') }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ __('dashboard.phone') }}</td>
                <td>{{ $employee->phone }}</td>
            </tr>
            <tr>
                <td>{{ __('dashboard.bank_name') }}</td>
                <td>{{ $employee->bank_name }}</td>
                <td>{{ __('dashboard.account_number') }}</td>
                <td>{{ $employee->account_number }}</td>
            </tr>
        </table>

        <!-- Insurance -->
        <div class="section-title">{{ __('dashboard.insurance_details') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('dashboard.provider_name') }}</th>
                    <th>{{ __('dashboard.policy_number') }}</th>
                    <th>{{ __('dashboard.type') }}</th>
                    <th>{{ __('dashboard.start_date') }}</th>
                    <th>{{ __('dashboard.end_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($insurances as $insurance)
                    <tr>
                        <td>{{ $insurance->provider_name }}</td>
                        <td>{{ $insurance->policy_number }}</td>
                        <td>{{ $insurance->policy_type }}</td>
                        <td>{{ $insurance->start_date }}</td>
                        <td>{{ $insurance->expiry_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Insurance -->
        <div class="section-title">{{ __('dashboard.document_details') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('dashboard.type') }}</th>
                    <th>{{ __('dashboard.document_number') }}</th>
                    <th>{{ __('dashboard.issue_date') }}</th>
                    <th>{{ __('dashboard.expiry_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td>{{ $document->type }}</td>
                        <td>{{ $document->document_number }}</td>
                        <td>{{ $document->issue_date }}</td>
                        <td>{{ $document->expiration_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Attendance -->
        <div class="section-title">{{ __('dashboard.attendance_report') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('dashboard.date') }}</th>
                    <th>{{ __('dashboard.day') }}</th>
                    <th>{{ __('dashboard.check_in') }}</th>
                    <th>{{ __('dashboard.check_out') }}</th>
                    <th>{{ __('dashboard.hours') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</td>
                        <td>{{ $attendance->check_in }}</td>
                        <td>{{ $attendance->check_out }}</td>
                        <td>{{ $attendance->working_hours }}</td>
                        <td>{{ $attendance->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
<script>
    window.onload = function() {
        window.print();
    };
</script>
