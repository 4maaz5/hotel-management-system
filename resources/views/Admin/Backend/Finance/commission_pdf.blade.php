<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hotely</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #aaa;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>{{ __('dashboard.commission_report') }}</h2>

    @if (isset($records) && $records->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.employee_name') }}</th>
                    <th>{{ __('dashboard.branch') }}</th>
                    <th>{{ __('dashboard.sale_amount') }}</th>
                    <th>{{ __('dashboard.commission') }} %</th>
                    <th>{{ __('dashboard.commission_earned') }}</th>
                    <th>{{ __('dashboard.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $totalCommission = 0; @endphp
                @foreach ($records as $index => $record)
                    @php
                        $commission = ($record->amount * $record->employee->commission_percentage) / 100;
                        $totalCommission += $commission;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                        <td>{{ $record->branch->name }}</td>
                        <td>{{ number_format($record->amount, 2) }}</td>
                        <td>{{ $record->employee->commission_percentage ?? 0 }}%</td>
                        <td>{{ number_format($commission, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->income_date)->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;">{{ __('dashboard.total_commission') }}</td>
                    <td>{{ number_format($totalCommission, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>
    @else
        {{-- <p>No records found for the selected filter.</p> --}}
    @endif

</body>

</html>
