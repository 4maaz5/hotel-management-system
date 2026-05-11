@extends('layout.master')
@section('title', 'Dashboard | Absent')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.employee_absentees') }}</h1>
        <div class="absent-grid">
            @foreach ($absentEmployeesAll as $absentEmployee)
                <div class="absent-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <span class="badge bg-dark">#{{ $loop->iteration }}</span>

                            <h6 class="fw-bold mt-2">
                                {{ optional($absentEmployee->employee)->first_name ?? '-' }}
                                {{ optional($absentEmployee->employee)->last_name ?? '' }}
                            </h6>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.employee_id') }}:</strong>
                                {{ optional($absentEmployee->employee)->employee_id ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.branch') }}:</strong>
                                {{ optional(optional($absentEmployee->employee)->branch)->name ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.department') }}:</strong>
                                {{ optional(optional($absentEmployee->employee)->department)->name ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.date') }}:</strong>
                                {{ $absentEmployee->date ?? '-' }}
                            </p>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $absentEmployeesAll->links('pagination::bootstrap-5') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.absentees') }}</h4>
                                {{-- <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addOvertimeModal">
                                    Add
                                </button> --}}
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.employee') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.department') }}</th>
                                                <th>{{ __('dashboard.date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($absentEmployees as $absentEmployee)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ optional($absentEmployee->employee)->first_name ?? '-' }}<br>
                                                        {{ optional($absentEmployee->employee)->employee_id ?? '-' }}
                                                    </td>
                                                    <td>{{ optional(optional($absentEmployee->employee)->branch)->name ?? '-' }}
                                                    </td>
                                                    <td>{{ optional(optional($absentEmployee->employee)->department)->name ?? '-' }}
                                                    </td>
                                                    <td>{{ $absentEmployee->date ?? '-' }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    <div id="absentPagination" class="d-flex justify-content-center mt-3">
                                        {{ $absentEmployees->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
