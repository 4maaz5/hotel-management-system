@extends('admin.reports.layouts.report')

@section('title', __('dashboard.occupancy_report'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_units') }}</small>
                        <h4 class="text-primary mb-0">{{ $totalUnits ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 text-center bg-primary text-white">
                        <small>{{ __('dashboard.occupancy_rate') }}</small>
                        <h2 class="mb-0">{{ $occupancyRate ?? 0 }}%</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.units') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.room_no') }}</th>
                            <th>{{ __('dashboard.floor') }}</th>
                            <th>{{ __('dashboard.room_type') }}</th>
                            <th>Occupied Days</th>
                            <th>{{ __('dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units ?? [] as $unit)
                            <tr>
                                <td>{{ $unit->unit_number ?? '-' }}</td>
                                <td>{{ $unit->floor->name ?? '-' }}</td>
                                <td>{{ $unit->unitType->name ?? '-' }}</td>
                                <td>{{ $unit->occupied_days ?? 0 }}</td>
                                <td>
                                    @if($unit->is_occupied_in_range)
                                        <span class="badge bg-success">{{ __('dashboard.occupied') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('dashboard.vacant') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
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
    <h4 class="text-center mb-3">{{ __('dashboard.occupancy_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_units') }}</td>
            <td class="text-center">{{ $totalUnits ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.occupancy_rate') }}</td>
            <td class="text-center">{{ $occupancyRate ?? 0 }}%</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.room_no') }}</th>
                <th>{{ __('dashboard.floor') }}</th>
                <th>{{ __('dashboard.room_type') }}</th>
                <th>Occupied Days</th>
                <th>{{ __('dashboard.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units ?? [] as $unit)
                <tr>
                    <td>{{ $unit->unit_number ?? '-' }}</td>
                    <td>{{ $unit->floor->name ?? '-' }}</td>
                    <td>{{ $unit->unitType->name ?? '-' }}</td>
                    <td>{{ $unit->occupied_days ?? 0 }}</td>
                    <td>{{ $unit->is_occupied_in_range ? __('dashboard.occupied') : __('dashboard.vacant') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.occupancy_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_units') }}</td>
            <td class="text-center">{{ $totalUnits ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.occupancy_rate') }}</td>
            <td class="text-center">{{ $occupancyRate ?? 0 }}%</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.room_no') }}</th>
                <th>{{ __('dashboard.floor') }}</th>
                <th>{{ __('dashboard.room_type') }}</th>
                <th>Occupied Days</th>
                <th>{{ __('dashboard.status_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units ?? [] as $unit)
                <tr>
                    <td>{{ $unit->unit_number ?? '-' }}</td>
                    <td>{{ $unit->floor->name ?? '-' }}</td>
                    <td>{{ $unit->unitType->name ?? '-' }}</td>
                    <td>{{ $unit->occupied_days ?? 0 }}</td>
                    <td>{{ $unit->is_occupied_in_range ? __('dashboard.occupied') : __('dashboard.vacant') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
