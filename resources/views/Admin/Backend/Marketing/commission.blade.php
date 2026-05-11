@extends('layout.master')
@section('title', 'Dashboard | Commission')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_commissions') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.commissions') }}</h4>

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.agent') }}</th>
                                                <th>{{ __('dashboard.quotation_number') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.commission') }} %</th>
                                                <th>{{ __('dashboard.commission_amount') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.created_at') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($commissions as $commission)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <td>
                                                        {{ $commission->agent?->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $commission->quotation?->quotation_number ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $commission->branch?->name ?? '-' }}
                                                    </td>

                                                    <td>
                                                        {{ $commission->commission_percentage }}%
                                                    </td>

                                                    <td>
                                                        {{ number_format($commission->commission_amount, 2) }}
                                                    </td>

                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $commission->paid_status === 'paid' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($commission->paid_status) }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        {{ $commission->created_at->format('Y-m-d') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">

                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

@endsection
