@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.employee_profile') }}</h4>

                            </div>
                            <div class="card-body">
                                <!-- Profile Header Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-center mb-4 pb-4 border-bottom">
                                            <div class="profile-image-container mb-3">
                                                <img src="{{ $employee->image ? asset('storage/' . $employee->image) : 'https://upload.wikimedia.org/wikipedia/commons/a/ab/Logo_TV_2022.svg' }}"
                                                    alt="Profile Picture" class="rounded-circle shadow"
                                                    style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #fff;">


                                            </div>
                                            <h3 class="mb-1">
                                                {{ $employee->first_name . $employee->last_name ?? '-' }}</h3>
                                            <p class="text-muted mb-2">{{ __('dashboard.designation') }}:
                                                {{ $employee->designation ?? '-' }}</p>

                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Details Grid -->
                                <div class="row">
                                    <!-- Personal Information Card -->
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-user"></i> {{ __('dashboard.personal_information') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="profile-item mb-3">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-id-badge text-primary"></i>
                                                        {{ __('dashboard.employee_id') }}
                                                    </label>
                                                    <p class="font-weight-bold">{{ $employee->employee_id ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="profile-item mb-3">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-birthday-cake text-primary"></i>
                                                        {{ __('dashboard.join_date') }}
                                                    </label>
                                                    <p class="font-weight-bold">{{ $employee->join_date ?? '-' }}
                                                    </p>
                                                </div>

                                                <div class="profile-item mb-3">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-ring text-primary"></i>
                                                        {{ __('dashboard.branch') }}
                                                    </label>
                                                    <p class="font-weight-bold">{{ $employee->branch->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="profile-item">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-flag text-primary"></i>
                                                        {{ __('dashboard.department') }}
                                                    </label>
                                                    <p class="font-weight-bold">
                                                        {{ $employee->department->name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Information Card -->
                                    <div class="col-lg-6 col-md-12 mb-4">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-success text-white">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-address-book"></i>
                                                    {{ __('dashboard.contact_information') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="profile-item mb-3">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-envelope text-success"></i>
                                                        {{ __('dashboard.email') }}
                                                    </label>
                                                    <p class="font-weight-bold">

                                                        {{ $employee->email ?? '-' }}

                                                    </p>
                                                </div>
                                                <div class="profile-item mb-3">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-phone text-success"></i>
                                                        {{ __('dashboard.phone_number') }}
                                                    </label>
                                                    <p class="font-weight-bold">

                                                        {{ $employee->phone ?? '-' }}

                                                    </p>
                                                </div>

                                                <div class="profile-item">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-map-marker-alt text-success"></i>
                                                        {{ __('dashboard.bank_name') }}
                                                    </label>
                                                    <p class="font-weight-bold">
                                                        {{ $employee->bank_name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="profile-item">
                                                    <label class="text-muted mb-1">
                                                        <i class="fas fa-map-marker-alt text-success"></i>
                                                        {{ __('dashboard.account_number') }}
                                                    </label>
                                                    <p class="font-weight-bold">
                                                        {{ $employee->account_number ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Left Table: Insurance Details -->
                                        <div class="col-md-6 text-center mb-3">
                                            <h5>{{ __('dashboard.insurance_details') }}</h5>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('dashboard.provider_name') }}</th>
                                                        <th>{{ __('dashboard.policy_number') }}</th>
                                                        <th>{{ __('dashboard.policy_type') }}</th>
                                                        <th>{{ __('dashboard.start_date') }}</th>
                                                        <th>{{ __('dashboard.expiry_date') }}</th>
                                                        <th>{{ __('dashboard.documents') }}</th>
                                                        {{-- <th>Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($insurances as $insurance)
                                                        <tr>
                                                            <td>{{ $insurance->employees->first_name . ' ' . $insurance->employees->last_name }}
                                                            </td>
                                                            <td>{{ $insurance->provider_name }}</td>
                                                            <td>{{ $insurance->policy_type }}</td>
                                                            <td>{{ $insurance->start_date }}</td>
                                                            <td>{{ $insurance->expiry_date }}</td>
                                                            <td>
                                                                @if ($insurance->document)
                                                                    <div class="text-center my-2">
                                                                        <a href="#" class="view-pdf"
                                                                            data-file="{{ asset('storage/' . $insurance->document) }}"
                                                                            title="View PDF">
                                                                            <i class="fas fa-file-pdf text-secondary"
                                                                                style="font-size: 28px;"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            {{-- <td>
                                                                <a href="#" class="text-secondary editEmployeeDocBtn">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <a href="#" class="text-danger deleteEmployeeDocBtn">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td> --}}
                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Right Table: Documents -->
                                        <div class="col-md-6 text-center mb-3">
                                            <h5>{{ __('dashboard.documents') }}</h5>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('dashboard.document_type') }}</th>
                                                        <th>{{ __('dashboard.document_number') }}</th>

                                                        <th>{{ __('dashboard.issue_date') }}</th>
                                                        <th>{{ __('dashboard.expiry_date') }}</th>
                                                        <th>{{ __('dashboard.view_document') }}</th>
                                                        {{-- <th>Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($documents as $document)
                                                        <tr>

                                                            <td>{{ $document->type }}</td>
                                                            <td>{{ $document->document_number }}</td>
                                                            <td>{{ $document->issue_date }}</td>
                                                            <td>{{ $document->expiration_date }}</td>
                                                            <td>
                                                                @if ($document->file_path)
                                                                    <div class="text-center my-2">
                                                                        <a href="#" class="view-pdf"
                                                                            data-file="{{ asset('storage/' . $document->file_path) }}"
                                                                            title="View PDF">
                                                                            <i class="fas fa-file-pdf text-secondary"
                                                                                style="font-size: 28px;"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                    @empty
                                                    @endforelse

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">

                                                <thead class="thead-light">
                                                    <tr>
                                                        <th colspan="8" class="text-center py-3 h4">
                                                            {{ __('dashboard.attendance_report') }}
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('dashboard.date') }}</th>
                                                        <th>{{ __('dashboard.day') }}</th>
                                                        <th>{{ __('dashboard.check_in') }}</th>
                                                        <th>{{ __('dashboard.check_out') }}</th>
                                                        <th>{{ __('dashboard.working_hours') }}</th>
                                                        <th>{{ __('dashboard.overtime') }}</th>
                                                        <th>{{ __('dashboard.status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($attendances as $attendance)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $attendance->date ?? '-' }}</td>
                                                            <td>
                                                                {{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('l') : '-' }}
                                                            </td>

                                                            <td>
                                                                <span class="text-success">
                                                                    <i class="fas fa-sign-in-alt"></i>
                                                                    {{ $attendance->check_in ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="text-danger">
                                                                    <i class="fas fa-sign-out-alt"></i>
                                                                    {{ $attendance->check_out ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $workingTime = '-';
                                                                    if (
                                                                        $attendance->check_in &&
                                                                        $attendance->check_out
                                                                    ) {
                                                                        $minutes = \Carbon\Carbon::parse(
                                                                            $attendance->check_in,
                                                                        )->diffInMinutes(
                                                                            \Carbon\Carbon::parse(
                                                                                $attendance->check_out,
                                                                            ),
                                                                        );
                                                                        $workingTime =
                                                                            floor($minutes / 60) .
                                                                            'h ' .
                                                                            $minutes % 60 .
                                                                            'm';
                                                                    }
                                                                @endphp

                                                                <strong>{{ $workingTime }}</strong>


                                                            </td>
                                                            <td>{{ $attendance->overtime_hours }}</td>
                                                            <td>
                                                                @if (($attendance->status ?? 'Present') == 'Present')
                                                                    <span class="badge badge-success">
                                                                        <i class="fas fa-check-circle"></i>
                                                                        {{ __('dashboard.present') }}
                                                                    </span>
                                                                @elseif(($attendance->status ?? '') == 'Absent')
                                                                    <span class="badge badge-danger">
                                                                        <i class="fas fa-times-circle"></i>
                                                                        {{ __('dashboard.absent') }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-secondary">
                                                                        <i class="fas fa-calendar"></i>
                                                                        {{ __('dashboard.leave') }}
                                                                    </span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    @php
                                                        $totalMinutes = 0;

                                                        foreach ($attendances as $att) {
                                                            if ($att->check_in && $att->check_out) {
                                                                $totalMinutes += \Carbon\Carbon::parse(
                                                                    $att->check_in,
                                                                )->diffInMinutes(
                                                                    \Carbon\Carbon::parse($att->check_out),
                                                                );
                                                            }
                                                        }

                                                        $totalHoursFormatted =
                                                            floor($totalMinutes / 60) . 'h ' . $totalMinutes % 60 . 'm';
                                                    @endphp

                                                    <tr>
                                                        <th colspan="5" class="text-right">
                                                            {{ __('dashboard.total_working_hours') }}</th>
                                                        <th><strong>{{ $totalHoursFormatted }}</strong>
                                                        </th>
                                                        <th colspan="2"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12 text-center">
                                                <a href="{{ route('dashboard.employee.index') }}"
                                                    class="btn btn-secondary mr-2">
                                                    <i class="fas fa-arrow-left"></i> {{ __('dashboard.back') }}
                                                </a>

                                                <a href="{{ route('dashboard.employee.pdf', $employee->id) }}"
                                                    class="btn btn-primary mr-2" target="_blank">
                                                    <i class="fas fa-print"></i> {{ __('dashboard.print') }}
                                                </a>

                                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('manager'))
                                                    <button class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteProfileModal">
                                                        <i class="fas fa-trash"></i> {{ __('dashboard.delete_profile') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    <!-- PDF Modal -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.view_pdf') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe src="" id="pdfFrame" frameborder="0" style="width:100%; height:600px;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Employee Modal -->
    <div class="modal fade" id="deleteProfileModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteEmployeeModalLabel">Delete Employee</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="deleteEmployeeForm" action="{{ route('dashboard.employee.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteEmployeeId" name="id" value={{ $employee->id }}>

                    <div class="modal-body text-center">
                        <p class="mb-0">{{ __('dashboard.confirm_delete_modal') }}?</p>
                    </div>

                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        $(document).on('click', '.view-pdf', function(e) {
            e.preventDefault();
            let file = $(this).data('file'); // get PDF URL from clicked row
            $('#pdfFrame').attr('src', file); // set iframe src
            $('#pdfModal').modal('show'); // show modal
        });

        // Clear iframe when modal closes
        $('#pdfModal').on('hidden.bs.modal', function() {
            $('#pdfFrame').attr('src', '');
        });
    </script>
@endsection
