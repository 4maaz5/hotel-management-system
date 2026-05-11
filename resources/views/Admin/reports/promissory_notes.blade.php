@extends('admin.reports.layouts.report')

@section('title', __('dashboard.promissory_notes_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.promissory_notes') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.note_no') }}</th>
                            <th>{{ __('dashboard.issue_date') }}</th>
                            <th>{{ __('dashboard.maturity_date') }}</th>
                            <th>{{ __('dashboard.guest') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                            <th class="text-end">{{ __('dashboard.paid') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promissoryNotes ?? [] as $note)
                            <tr>
                                <td>{{ $note->note_number ?? '#' . $note->id }}</td>
                                <td>{{ $note->date->format('Y-m-d') }}</td>
                                <td>{{ $note->maturity_date ? $note->maturity_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                                <td class="text-end">SAR {{ number_format($note->amount, 2) }}</td>
                                <td class="text-end">SAR {{ number_format($note->paid_amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $note->status === 'collected' ? 'success' : ($note->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($note->status ?? 'pending') }}
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
        @if(isset($promissoryNotes) && $promissoryNotes->hasPages())
            <div class="card-footer">
                {{ $promissoryNotes->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.promissory_notes_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.note_no') }}</th>
                <th>{{ __('dashboard.issue_date') }}</th>
                <th>{{ __('dashboard.maturity_date') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
                <th class="text-end">{{ __('dashboard.paid') }}</th>
                <th>{{ __('dashboard.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promissoryNotes ?? [] as $note)
                <tr>
                    <td>{{ $note->note_number ?? '#' . $note->id }}</td>
                    <td>{{ $note->date->format('Y-m-d') }}</td>
                    <td>{{ $note->maturity_date ? $note->maturity_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                    <td class="text-end">SAR {{ number_format($note->amount, 2) }}</td>
                    <td class="text-end">SAR {{ number_format($note->paid_amount ?? 0, 2) }}</td>
                    <td>{{ ucfirst($note->status ?? 'pending') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.promissory_note_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.note_no') }}</th>
                <th>{{ __('dashboard.issue_date') }}</th>
                <th>{{ __('dashboard.maturity_date') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
                <th class="text-end">{{ __('dashboard.paid') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($promissoryNotes ?? [] as $note)
                <tr>
                    <td>{{ $note->note_number ?? '#' . $note->id }}</td>
                    <td>{{ $note->date->format('Y-m-d') }}</td>
                    <td>{{ $note->maturity_date ? $note->maturity_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td>
                    <td class="text-end">ر.س {{ number_format($note->amount, 2) }}</td>
                    <td class="text-end">ر.س {{ number_format($note->paid_amount ?? 0, 2) }}</td>
                    <td>{{ ucfirst($note->status ?? 'pending') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
