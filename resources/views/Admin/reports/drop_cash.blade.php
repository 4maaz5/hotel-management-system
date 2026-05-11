@extends('admin.reports.layouts.report')

@section('title', __('dashboard.drop_cash_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.drop_cash_vouchers') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.voucher_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.paid_to') }}</th>
                            <th>{{ __('dashboard.method') }}</th>
                            <th>{{ __('dashboard.description') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dropCash ?? [] as $drop)
                            <tr>
                                <td>{{ $drop->voucher_number }}</td>
                                <td>{{ $drop->date_from->format('Y-m-d') }}</td>
                                <td>{{ $drop->paid_to ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $drop->drop_method === 'cash' ? 'info' : 'primary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $drop->drop_method)) }}
                                    </span>
                                </td>
                                <td>{{ $drop->description ?? '-' }}</td>
                                <td class="text-end text-danger">SAR {{ number_format($drop->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">{{ __('dashboard.total') }}</td>
                            <td class="text-end fw-bold">SAR {{ number_format(($dropCash->sum('amount') ?? 0), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @if(isset($dropCash) && $dropCash->hasPages())
            <div class="card-footer">
                {{ $dropCash->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.drop_cash_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.paid_to') }}</th>
                <th>{{ __('dashboard.method') }}</th>
                <th>{{ __('dashboard.description') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dropCash ?? [] as $drop)
                <tr>
                    <td>{{ $drop->voucher_number }}</td>
                    <td>{{ $drop->date_from->format('Y-m-d') }}</td>
                    <td>{{ $drop->paid_to ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $drop->drop_method)) }}</td>
                    <td>{{ $drop->description ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($drop->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td colspan="5" class="text-end">{{ __('dashboard.total') }}</td>
                <td class="text-end">SAR {{ number_format(($dropCash->sum('amount') ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.drop_cash_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no_ar') }}</th>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th>{{ __('dashboard.paid_to_ar') }}</th>
                <th>{{ __('dashboard.method') }}</th>
                <th>{{ __('dashboard.description') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dropCash ?? [] as $drop)
                <tr>
                    <td>{{ $drop->voucher_number }}</td>
                    <td>{{ $drop->date_from->format('Y-m-d') }}</td>
                    <td>{{ $drop->paid_to ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $drop->drop_method)) }}</td>
                    <td>{{ $drop->description ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($drop->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td colspan="5" class="text-end">{{ __('dashboard.total_ar') }}</td>
                <td class="text-end">ر.س {{ number_format(($dropCash->sum('amount') ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
