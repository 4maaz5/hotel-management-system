@extends('admin.reports.layouts.report')

@section('title', __('dashboard.night_audit_history_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.night_audit_history') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.start_date_time') }}</th>
                            <th>{{ __('dashboard.end_date_time') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.user') }}</th>
                            <th>{{ __('dashboard.period_date_from') }}</th>
                            <th>{{ __('dashboard.period_date_to') }}</th>
                            <th>{{ __('dashboard.night_count') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                            <tr>
                                <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                                <td>
                                    @if($audit->status === 'completed')
                                        <span class="badge bg-success">{{ __('dashboard.completed') }}</span>
                                    @elseif($audit->status === 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('dashboard.failed') }}</span>
                                    @endif
                                </td>
                                <td>{{ $audit->user->name ?? '-' }}</td>
                                <td>{{ $audit->period_date_from->format('Y-m-d') }}</td>
                                <td>{{ $audit->period_date_to->format('Y-m-d') }}</td>
                                <td>{{ $audit->night_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($audits->hasPages())
            <div class="card-footer">
                {{ $audits->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.night_audit_history_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.start_date_time') }}</th>
                <th>{{ __('dashboard.end_date_time') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.user') }}</th>
                <th>{{ __('dashboard.period_date_from') }}</th>
                <th>{{ __('dashboard.period_date_to') }}</th>
                <th>{{ __('dashboard.night_count') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audits as $audit)
                <tr>
                    <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ ucfirst($audit->status) }}</td>
                    <td>{{ $audit->user->name ?? '-' }}</td>
                    <td>{{ $audit->period_date_from->format('Y-m-d') }}</td>
                    <td>{{ $audit->period_date_to->format('Y-m-d') }}</td>
                    <td>{{ $audit->night_count }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.night_audit_history_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.start_date_time_ar') }}</th>
                <th>{{ __('dashboard.end_date_time_ar') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
                <th>{{ __('dashboard.user_ar') }}</th>
                <th>{{ __('dashboard.start_date_ar') }}</th>
                <th>{{ __('dashboard.end_date_ar') }}</th>
                <th>{{ __('dashboard.night_count') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audits as $audit)
                <tr>
                    <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ ucfirst($audit->status) }}</td>
                    <td>{{ $audit->user->name ?? '-' }}</td>
                    <td>{{ $audit->period_date_from->format('Y-m-d') }}</td>
                    <td>{{ $audit->period_date_to->format('Y-m-d') }}</td>
                    <td>{{ $audit->night_count }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
