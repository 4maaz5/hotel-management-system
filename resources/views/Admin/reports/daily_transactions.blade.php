@extends('admin.reports.layouts.report')

@section('title', __('dashboard.daily_transactions_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.daily_transactions') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.date') }}</th>
                            <th class="text-center">{{ __('dashboard.receipts_count') }}</th>
                            <th class="text-end">{{ __('dashboard.receipts_amount') }}</th>
                            <th class="text-center">{{ __('dashboard.payments_count') }}</th>
                            <th class="text-end">{{ __('dashboard.payments_amount') }}</th>
                            <th class="text-end">{{ __('dashboard.net') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyData ?? [] as $data)
                            <tr>
                                <td>{{ $data['date'] }}</td>
                                <td class="text-center">{{ $data['receipts_count'] }}</td>
                                <td class="text-end text-success">SAR {{ number_format($data['receipts_amount'], 2) }}</td>
                                <td class="text-center">{{ $data['payments_count'] }}</td>
                                <td class="text-end text-danger">SAR {{ number_format($data['payments_amount'], 2) }}</td>
                                <td class="text-end {{ $data['receipts_amount'] - $data['payments_amount'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    SAR {{ number_format($data['receipts_amount'] - $data['payments_amount'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.daily_transactions') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.date') }}</th>
                <th class="text-center">{{ __('dashboard.receipts_count') }}</th>
                <th class="text-end">{{ __('dashboard.receipts_amount') }}</th>
                <th class="text-center">{{ __('dashboard.payments_count') }}</th>
                <th class="text-end">{{ __('dashboard.payments_amount') }}</th>
                <th class="text-end">{{ __('dashboard.net') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyData ?? [] as $data)
                <tr>
                    <td>{{ $data['date'] }}</td>
                    <td class="text-center">{{ $data['receipts_count'] }}</td>
                    <td class="text-end">SAR {{ number_format($data['receipts_amount'], 2) }}</td>
                    <td class="text-center">{{ $data['payments_count'] }}</td>
                    <td class="text-end">SAR {{ number_format($data['payments_amount'], 2) }}</td>
                    <td class="text-end">SAR {{ number_format($data['receipts_amount'] - $data['payments_amount'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.daily_transactions_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th class="text-center">{{ __('dashboard.receipts_count') }}</th>
                <th class="text-end">{{ __('dashboard.receipts_amount') }}</th>
                <th class="text-center">{{ __('dashboard.payments_count') }}</th>
                <th class="text-end">{{ __('dashboard.payments_amount') }}</th>
                <th class="text-end">{{ __('dashboard.net') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyData ?? [] as $data)
                <tr>
                    <td>{{ $data['date'] }}</td>
                    <td class="text-center">{{ $data['receipts_count'] }}</td>
                    <td class="text-end">ر.س {{ number_format($data['receipts_amount'], 2) }}</td>
                    <td class="text-center">{{ $data['payments_count'] }}</td>
                    <td class="text-end">ر.س {{ number_format($data['payments_amount'], 2) }}</td>
                    <td class="text-end">ر.س {{ number_format($data['receipts_amount'] - $data['payments_amount'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
