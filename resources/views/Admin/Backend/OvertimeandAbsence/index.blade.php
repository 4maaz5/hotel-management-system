@extends('layout.master')
@section('title', 'Dashboard | Overtime')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.employee_overtime') }}</h1>
        <div class="overtime-grid">
            @php $counter = 1; @endphp

            @foreach ($overtimeSummaryCards as $summary)
                <div class="overtime-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-dark">#{{ $counter++ }}</span>

                                <!-- VIEW ICON -->
                                <a href="#" class="text-secondary" title="View" data-toggle="modal"
                                    data-target="#viewOvertimeModal__{{ $summary['employee']->id }}"
                                    data-employee="{{ $summary['employee']->id }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>

                            <h6 class="fw-bold mt-2">
                                {{ $summary['employee']->first_name }} {{ $summary['employee']->last_name }}
                            </h6>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.branch') }}:</strong>
                                {{ $summary['employee']->branch->name ?? 'N/A' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.total_overtime') }}:</strong>
                                {{ number_format($summary['total_overtime'], 2) }}
                            </p>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $overtimeSummaryCards->links('pagination::bootstrap-5') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.overtime') }}</h4>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.employee') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.total_hours') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $counter = 1; @endphp
                                            @foreach ($overtimeSummaryCards as $summary)
                                                <tr>
                                                    <td>{{ $counter++ }}</td>
                                                    <td>{{ $summary['employee']->first_name }}
                                                        {{ $summary['employee']->last_name }}</td>
                                                    <td>{{ $summary['employee']->branch->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format($summary['total_overtime'], 2) }} </td>
                                                    <td> <a href="#" class="text-secondary me-2" title="View"
                                                            data-toggle="modal"
                                                            data-target="#viewOvertimeModal__{{ $summary['employee']->id }}"
                                                            data-employee="{{ $summary['employee']->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div id="overtimePagination" class="d-flex justify-content-center mt-3">
                                        {{ $overtimeSummaryCards->links('pagination::bootstrap-5') }}
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- View Overtime Modal -->
        @foreach ($overtimeSummary as $summary)
            <div class="modal fade" id="viewOvertimeModal__{{ $summary['employee']->id }}" tabindex="-1" role="dialog"
                aria-labelledby="viewOvertimeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="viewOvertimeModalLabel">{{ __('dashboard.overtime_details') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <h5 class="mt-2">{{ $summary['employee']->first_name }}
                                    {{ $summary['employee']->last_name }}</h5>
                                <p class="text-muted">
                                    {{ $summary['employee']->employee_id ?? 'N/A' }} |
                                    {{ $summary['employee']->branch->name ?? 'N/A' }} |
                                    {{ $summary['employee']->designation ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('dashboard.employee') }}</th>
                                            <th>{{ __('dashboard.branch') }}</th>
                                            <th>{{ __('dashboard.designation') }}</th>
                                            <th>{{ __('dashboard.department') }}</th>
                                            <th>{{ __('dashboard.total_ot') }} (hrs)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>{{ $summary['employee']->first_name }}
                                                {{ $summary['employee']->last_name }}</td>
                                            <td>{{ $summary['employee']->branch->name ?? 'N/A' }}</td>
                                            <td>{{ $summary['employee']->designation ?? 'N/A' }}</td>
                                            <td>{{ $summary['employee']->department->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($summary['total_overtime'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                data-dismiss="modal">{{ __('dashboard.close') }}</button>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
