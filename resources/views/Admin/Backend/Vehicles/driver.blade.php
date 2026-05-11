@extends('layout.master')
@section('title', 'Dashboard | Drivers')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_drivers') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.drivers') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addDriverModal">
                                    {{ __('dashboard.add_driver') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.driver_name') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.id_number') }}</th>
                                                <th>{{ __('dashboard.vehicle') }}</th>
                                                <th>{{ __('dashboard.driver_documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($drivers as $driver)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $driver->name }}</td>
                                                    <td>{{ $driver->phone ?? '-' }}</td>
                                                    <td>{{ $driver->id_number ?? '-' }}</td>
                                                    <td>
                                                        @if ($driver->vehicle)
                                                            {{ $driver->vehicle->name }} -
                                                            {{ $driver->vehicle->plate_number }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($driver->documents->count())
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($driver->documents as $doc)
                                                                    <div class="me-2 mb-1">
                                                                        <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                                            target="_blank">
                                                                            <i
                                                                                class="fas fa-file-alt fa-lg text-secondary ml-2"></i>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editDriverModal{{ $driver->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteDriverModal_{{ $driver->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Add Driver Modal -->
        <div class="modal fade" id="addDriverModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form action="{{ route('dashboard.company.driver.store') }}" method="POST" enctype="multipart/form-data"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.add_driver') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">

                                <!-- Vehicle Select Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="vehicle_id">{{ __('dashboard.select_vehicle') }}</label>
                                    <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_vehicle') }}</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ (old('vehicle_id') ?? ($driver->vehicle_id ?? '')) == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->name }} - {{ $vehicle->plate_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Driver Name -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.driver_name') }}</label>
                                    <input type="text" name="name" class="form-control">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>

                                <!-- ID Number -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.id_number') }}</label>
                                    <input type="text" name="id_number" class="form-control">
                                </div>



                                <!-- ============ Driver Documents ============ -->
                                <div class="col-md-12 mt-2">
                                    <label>{{ __('dashboard.driver_documents') }}</label>

                                    <div id="driver-document-wrapper">

                                        <!-- First Document Block -->
                                        <div class="mb-3 driver-document-input border p-2 rounded">
                                            <div class="row no-gutters align-items-center">

                                                <!-- File -->
                                                <div class="col-md-4 pr-2">
                                                    <input type="file" class="form-control" name="driver_files[]">
                                                </div>

                                                <!-- Start Date -->
                                                <div class="col-md-3 pr-2">
                                                    <input type="date" class="form-control" name="driver_start_date[]">
                                                </div>

                                                <!-- End Date -->
                                                <div class="col-md-3 pr-2">
                                                    <input type="date" class="form-control" name="driver_end_date[]">
                                                </div>

                                                <!-- Add -->
                                                <div class="col-md-2 text-center">
                                                    <button type="button"
                                                        class="btn btn-success btn-sm add-driver-document-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <!-- Error -->
                                                <div class="col-12 mt-1">
                                                    <span class="text-danger error-text driver_file_error"></span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>


        @foreach ($drivers as $driver)
            <!-- Edit Driver Modal -->
            <div class="modal fade" id="editDriverModal{{ $driver->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <form action="{{ route('dashboard.company.driver.update', $driver->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_driver') }}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">

                                    <!-- Vehicle Select Dropdown -->
                                    <div class="col-md-6 mb-3">
                                        <label for="vehicle_id">{{ __('dashboard.select_vehicle') }}</label>
                                        <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_vehicle') }}</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $driver->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->name }} - {{ $vehicle->plate_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Driver Name -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.driver_name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $driver->name }}" required>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $driver->phone }}">
                                    </div>

                                    <!-- ID Number -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.id_number') }}</label>
                                        <input type="text" name="id_number" class="form-control"
                                            value="{{ $driver->id_number }}">
                                    </div>

                                    <!-- Existing Driver Documents -->
                                    <div class="col-md-12 mt-2">
                                        <label>{{ __('dashboard.existing_documents') }}</label>
                                        <div class="d-flex flex-wrap mb-2">
                                            @foreach ($driver->documents as $doc)
                                                <div class="me-2 mb-1">
                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                                        <i class="fas fa-file-alt fa-lg text-secondary ml-2"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Add New Documents -->
                                    <div class="col-md-12 mt-2">
                                        <label>{{ __('dashboard.add_new_documents') }}</label>
                                        <div class="driver-document-wrapper">
                                            <div class="mb-3 driver-document-input border p-2 rounded">
                                                <div class="row no-gutters align-items-center">

                                                    <div class="col-md-4 pr-2">
                                                        <input type="file" class="form-control" name="driver_files[]">
                                                    </div>

                                                    <div class="col-md-3 pr-2">
                                                        <input type="date" class="form-control"
                                                            name="driver_start_date[]">
                                                    </div>

                                                    <div class="col-md-3 pr-2">
                                                        <input type="date" class="form-control"
                                                            name="driver_end_date[]">
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <button type="button"
                                                            class="btn btn-success btn-sm edit-add-driver-document-btn">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- Driver Delete Modals --}}
        @foreach ($drivers as $driver)
            <div class="modal fade" id="deleteDriverModal_{{ $driver->id }}" tabindex="-1"
                aria-labelledby="deleteDriverModalLabel_{{ $driver->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteDriverModalLabel_{{ $driver->id }}">
                                {{ __('dashboard.delete_driver') }}
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.driver.destroy', $driver->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $driver->name }}</strong>?
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach


    </div>
    <script>
        @if ($errors->any())
            $(document).ready(function() {
                $('#addDriverModal').modal('show');
            });
        @endif
    </script>
    <script>
        $(document).on('click', '.add-driver-document-btn', function() {
            let html = `
        <div class="mb-3 driver-document-input border p-2 rounded">
            <div class="row no-gutters align-items-center">

                <div class="col-md-4 pr-2">
                    <input type="file" class="form-control" name="driver_files[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="driver_start_date[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="driver_end_date[]">
                </div>

                <div class="col-md-2 text-center">
                    <button type="button"
                        class="btn btn-danger btn-sm remove-driver-document-btn">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>

                <div class="col-12 mt-1">
                    <span class="text-danger error-text driver_file_error"></span>
                </div>

            </div>
        </div>
    `;
            $('#driver-document-wrapper').append(html);
        });

        $(document).on('click', '.remove-driver-document-btn', function() {
            $(this).closest('.driver-document-input').remove();
        });

        // Add new driver document row in Edit Modal
        $(document).on('click', '.edit-add-driver-document-btn', function() {
            let html = `
    <div class="mb-3 driver-document-input border p-2 rounded">
        <div class="row no-gutters align-items-center">

            <div class="col-md-4 pr-2">
                <input type="file" class="form-control" name="driver_files[]">
            </div>

            <div class="col-md-3 pr-2">
                <input type="date" class="form-control" name="driver_start_date[]">
            </div>

            <div class="col-md-3 pr-2">
                <input type="date" class="form-control" name="driver_end_date[]">
            </div>

            <div class="col-md-2 text-center">
                <button type="button" class="btn btn-danger btn-sm remove-driver-document-btn">
                    <i class="fas fa-minus"></i>
                </button>
            </div>

        </div>
    </div>
    `;
            // Append to the correct wrapper inside the modal
            $(this).closest('.driver-document-wrapper').append(html);
        });

        // Remove driver document row
        $(document).on('click', '.remove-driver-document-btn', function() {
            $(this).closest('.driver-document-input').remove();
        });
    </script>



@endsection
