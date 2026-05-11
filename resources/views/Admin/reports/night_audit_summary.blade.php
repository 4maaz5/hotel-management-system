@extends('admin.reports.layouts.report')

@section('title', __('dashboard.night_audit_summary_report'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total') }}</small>
                        <h4 class="text-primary mb-0">{{ $summary['total'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.completed') }}</small>
                        <h4 class="text-success mb-0">{{ $summary['completed'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.pending') }}</small>
                        <h4 class="text-warning mb-0">{{ $summary['pending'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.failed') }}</small>
                        <h4 class="text-danger mb-0">{{ $summary['failed'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.night_audits') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.id') }}</th>
                            <th>{{ __('dashboard.start_date_time') }}</th>
                            <th>{{ __('dashboard.end_date_time') }}</th>
                            <th>{{ __('dashboard.user') }}</th>
                            <th>{{ __('dashboard.period') }}</th>
                            <th>{{ __('dashboard.night_count') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits ?? [] as $audit)
                            <tr>
                                <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $audit->user->name ?? '-' }}</td>
                                <td>{{ $audit->period_date_from->format('Y-m-d') }} - {{ $audit->period_date_to->format('Y-m-d') }}</td>
                                <td class="text-center">{{ $audit->night_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $audit->status === 'completed' ? 'success' : ($audit->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($audit->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
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
    <h4 class="text-center mb-3">{{ __('dashboard.night_audit_summary_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total') }}</td>
            <td class="text-center">{{ $summary['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.completed') }}</td>
            <td class="text-center">{{ $summary['completed'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.pending') }}</td>
            <td class="text-center">{{ $summary['pending'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.failed') }}</td>
            <td class="text-center">{{ $summary['failed'] ?? 0 }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.start_date_time') }}</th>
                <th>{{ __('dashboard.end_date_time') }}</th>
                <th>{{ __('dashboard.user') }}</th>
                <th>{{ __('dashboard.period') }}</th>
                <th>{{ __('dashboard.night_count') }}</th>
                <th>{{ __('dashboard.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audits ?? [] as $audit)
                <tr>
                    <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $audit->user->name ?? '-' }}</td>
                    <td>{{ $audit->period_date_from->format('Y-m-d') }} - {{ $audit->period_date_to->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $audit->night_count }}</td>
                    <td>{{ ucfirst($audit->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.night_audit_summary_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_ar') }}</td>
            <td class="text-center">{{ $summary['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.completed_ar') }}</td>
            <td class="text-center">{{ $summary['completed'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.pending_ar') }}</td>
            <td class="text-center">{{ $summary['pending'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.failed_ar') }}</td>
            <td class="text-center">{{ $summary['failed'] ?? 0 }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.start_date_time_ar') }}</th>
                <th>{{ __('dashboard.end_date_time_ar') }}</th>
                <th>{{ __('dashboard.user_ar') }}</th>
                <th>{{ __('dashboard.period') }}</th>
                <th>{{ __('dashboard.night_count') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audits ?? [] as $audit)
                <tr>
                    <td>#{{ str_pad($audit->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td>
                    <td>{{ $audit->end_date_time ? $audit->end_date_time->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $audit->user->name ?? '-' }}</td>
                    <td>{{ $audit->period_date_from->format('Y-m-d') }} - {{ $audit->period_date_to->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $audit->night_count }}</td>
                    <td>{{ ucfirst($audit->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
