@extends('admin.reports.layouts.report')

@section('title', __('dashboard.credit_notes_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.credit_notes') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.credit_note_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.guest') }}</th>
                            <th>{{ __('dashboard.reservation') }}</th>
                            <th>{{ __('dashboard.reason') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditNotes ?? [] as $note)
                            <tr>
                                <td>{{ $note->credit_note_number ?? '#' . $note->id }}</td>
                                <td>{{ $note->cn_date ? $note->cn_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                                <td>{{ $note->reservation->reservation_number ?? '-' }}</td>
                                <td>{{ $note->reason ?? '-' }}</td>
                                <td class="text-end text-warning">SAR {{ number_format($note->amount, 2) }}</td>
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
        @if(isset($creditNotes) && $creditNotes->hasPages())
            <div class="card-footer">
                {{ $creditNotes->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.credit_notes_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.credit_note_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th>{{ __('dashboard.reservation') }}</th>
                <th>{{ __('dashboard.reason') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($creditNotes ?? [] as $note)
                <tr>
                    <td>{{ $note->credit_note_number ?? '#' . $note->id }}</td>
                    <td>{{ $note->created_at }}</td>
                    <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                    <td>{{ $note->reservation->reservation_number ?? '-' }}</td>
                    <td>{{ $note->reason ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($note->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.credit_note_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.credit_note_no') }}</th>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th>{{ __('dashboard.reservation') }}</th>
                <th>{{ __('dashboard.reason') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($creditNotes ?? [] as $note)
                <tr>
                    <td>{{ $note->credit_note_number ?? '#' . $note->id }}</td>
                    <td>{{ $note->created_at }}</td>
                    <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                    <td>{{ $note->reservation->reservation_number ?? '-' }}</td>
                    <td>{{ $note->reason ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($note->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
