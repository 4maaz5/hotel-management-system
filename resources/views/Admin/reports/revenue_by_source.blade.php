@extends('admin.reports.layouts.report')

@section('title', __('dashboard.revenue_by_source_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.revenue_by_source') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.source') }}</th>
                            <th class="text-center">{{ __('dashboard.reservations') }}</th>
                            <th class="text-end">{{ __('dashboard.total_revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bySource ?? [] as $source => $data)
                            <tr>
                                <td>{{ $source }}</td>
                                <td class="text-center">{{ $data['count'] }}</td>
                                <td class="text-end">SAR {{ number_format($data['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td class="text-end fw-bold">{{ __('dashboard.total') }}</td>
                            <td class="text-center fw-bold">{{ ($bySource ? $bySource->sum('count') : 0) }}</td>
                            <td class="text-end fw-bold">SAR {{ number_format(($bySource ? $bySource->sum('total') : 0), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.revenue_by_source_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.source') }}</th>
                <th class="text-center">{{ __('dashboard.reservations') }}</th>
                <th class="text-end">{{ __('dashboard.total_revenue') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bySource ?? [] as $source => $data)
                <tr>
                    <td>{{ $source }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-end">SAR {{ number_format($data['total'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td class="text-end">{{ __('dashboard.total') }}</td>
                <td class="text-center">{{ ($bySource ? $bySource->sum('count') : 0) }}</td>
                <td class="text-end">SAR {{ number_format(($bySource ? $bySource->sum('total') : 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.revenue_by_source_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.source') }}</th>
                <th class="text-center">{{ __('dashboard.reservations_count') }}</th>
                <th class="text-end">{{ __('dashboard.total_revenue') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bySource ?? [] as $source => $data)
                <tr>
                    <td>{{ $source }}</td>
                    <td class="text-center">{{ $data['count'] }}</td>
                    <td class="text-end">ر.س {{ number_format($data['total'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td class="text-end">{{ __('dashboard.total_ar') }}</td>
                <td class="text-center">{{ ($bySource ? $bySource->sum('count') : 0) }}</td>
                <td class="text-end">ر.س {{ number_format(($bySource ? $bySource->sum('total') : 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
