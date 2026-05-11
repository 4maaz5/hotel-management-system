@extends('admin.reports.layouts.report')

@section('title', __('dashboard.housekeeping_status_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.housekeeping_status') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.room_no') }}</th>
                            <th>{{ __('dashboard.floor') }}</th>
                            <th>{{ __('dashboard.room_type') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units ?? [] as $unit)
                            <tr>
                                <td>{{ $unit->unit_number ?? '-' }}</td>
                                <td>{{ $unit->floor->name ?? '-' }}</td>
                                <td>{{ $unit->unitType->name ?? '-' }}</td>
                                <td>
                                    @if($unit->housekeeping_status)
                                        <span class="badge bg-secondary">
                                            {{ $unit->housekeeping_status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('dashboard.not_set') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
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
    <h4 class="text-center mb-3">{{ __('dashboard.housekeeping_status_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.room_no') }}</th>
                <th>{{ __('dashboard.floor') }}</th>
                <th>{{ __('dashboard.room_type') }}</th>
                <th>{{ __('dashboard.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units ?? [] as $unit)
                <tr>
                    <td>{{ $unit->unit_number ?? '-' }}</td>
                    <td>{{ $unit->floor->name ?? '-' }}</td>
                    <td>{{ $unit->unitType->name ?? '-' }}</td>
                    <td>{{ $unit->housekeeping_status ?? __('dashboard.not_set') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.housekeeping_status_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.room_no') }}</th>
                <th>{{ __('dashboard.floor') }}</th>
                <th>{{ __('dashboard.room_type') }}</th>
                <th>{{ __('dashboard.hk_status_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units ?? [] as $unit)
                <tr>
                    <td>{{ $unit->unit_number ?? '-' }}</td>
                    <td>{{ $unit->floor->name ?? '-' }}</td>
                    <td>{{ $unit->unitType->name ?? '-' }}</td>
                    <td>{{ $unit->housekeeping_status ?? __('dashboard.not_set') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
