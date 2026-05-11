@extends('layout.master')
@section('title', 'Dashboard | Vehicles')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_vehicles') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.vehicles') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addVehicleModal">
                                    {{ __('dashboard.add_vehicle') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.vehicle_name') }}</th>
                                                <th>{{ __('dashboard.model') }}</th>
                                                <th>{{ __('dashboard.plate_number') }}</th>
                                                <th>{{ __('dashboard.owner') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @forelse ($vehicles as $vehicle)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Branch -->
                                                    <td>{{ $vehicle->branch->name ?? '-' }}</td>

                                                    <!-- Vehicle Name -->
                                                    <td>{{ $vehicle->name }}</td>

                                                    <!-- Model -->
                                                    <td>{{ $vehicle->model ?? '-' }}</td>

                                                    <!-- Plate Number -->
                                                    <td>{{ $vehicle->plate_number }}</td>

                                                    <!-- Owner -->
                                                    <td>
                                                        {{ $vehicle->owner_name ?? '-' }} <br>
                                                        <small
                                                            class="text-muted">{{ $vehicle->owner_contact ?? '' }}</small>
                                                    </td>

                                                    <!-- Documents -->
                                                    <td>
                                                        @if ($vehicle->documents->count())
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($vehicle->documents as $doc)
                                                                    <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                                        target="_blank" class="me-2 mb-1"
                                                                        title="Valid till: {{ $doc->end_date ?? 'N/A' }}">
                                                                        <i class="fas fa-file-alt fa-lg text-secondary"></i>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>

                                                    <!-- Actions -->
                                                    <td>
                                                        <a href="#" class="text-secondary me-2" data-toggle="modal"
                                                            data-target="#editVehicleModal_{{ $vehicle->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteVehicleModal_{{ $vehicle->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
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


        <!-- Add Vehicle Modal -->
        <div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form action="{{ route('dashboard.company.vehicle.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">

                        <div class="modal-header ">
                            <h5 class="modal-title">{{ __('dashboard.add_vehicle') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">

                                <!-- Branch -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Vehicle Name -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.vehicle_name') }}</label>
                                    <input type="text" name="name" class="form-control">
                                    @error('name')
                                        <span class=" text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Model -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.model') }}</label>
                                    <input type="text" name="model" class="form-control">
                                </div>

                                <!-- Plate Number -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.plate_number') }}</label>
                                    <input type="text" name="plate_number" class="form-control">
                                    @error('plate_number')
                                        <span class=" text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Owner -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.owner_name') }}</label>
                                    <input type="text" name="owner_name" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.owner_contact') }}</label>
                                    <input type="text" name="owner_contact" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.owner_iqama') }}</label>
                                    <input type="text" name="owner_iqama" class="form-control">
                                </div>

                                <!-- Vehicle Documents + Dates Section -->
                                <div class="col-md-12">
                                    <label>{{ __('dashboard.vehicle_documents') }}</label>

                                    <div id="vehicle-document-wrapper">

                                        <!-- First Document Block -->
                                        <div class="mb-3 vehicle-document-input border p-2 rounded">
                                            <div class="row no-gutters align-items-center">

                                                <!-- File -->
                                                <div class="col-md-4 pr-2">
                                                    <input type="file" class="form-control" name="vehicle_files[]">
                                                </div>

                                                <!-- Start Date -->
                                                <div class="col-md-3 pr-2">
                                                    <input type="date" class="form-control"
                                                        name="vehicle_start_date[]">
                                                </div>

                                                <!-- End Date -->
                                                <div class="col-md-3 pr-2">
                                                    <input type="date" class="form-control" name="vehicle_end_date[]">
                                                </div>

                                                <!-- Add / Remove -->
                                                <div class="col-md-2 text-center">
                                                    <button type="button"
                                                        class="btn btn-success btn-sm add-vehicle-document-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <!-- Error -->
                                                <div class="col-12 mt-1">
                                                    <span class="text-danger error-text vehicle_file_error"></span>
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

        @foreach ($vehicles as $vehicle)
            <div class="modal fade" id="editVehicleModal_{{ $vehicle->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

                    <form action="{{ route('dashboard.company.vehicle.update', $vehicle->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_vehicle') }}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body">
                                <div class="row">

                                    <!-- Branch -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.branch') }}</label>
                                        <select name="branch_id" class="form-control" required>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $vehicle->branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Vehicle Name -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.vehicle_name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $vehicle->name }}">
                                    </div>

                                    <!-- Model -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.model') }}</label>
                                        <input type="text" name="model" class="form-control"
                                            value="{{ $vehicle->model }}">
                                    </div>

                                    <!-- Plate Number -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.plate_number') }}</label>
                                        <input type="text" name="plate_number" class="form-control"
                                            value="{{ $vehicle->plate_number }}">
                                    </div>

                                    <!-- Owner -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.owner_name') }}</label>
                                        <input type="text" name="owner_name" class="form-control"
                                            value="{{ $vehicle->owner_name }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.owner_contact') }}</label>
                                        <input type="text" name="owner_contact" class="form-control"
                                            value="{{ $vehicle->owner_contact }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.owner_iqama') }}</label>
                                        <input type="text" name="owner_iqama" class="form-control"
                                            value="{{ $vehicle->owner_iqama }}">
                                    </div>

                                    <!-- Existing Documents -->
                                    <div class="col-md-12 mb-3">
                                        <label>{{ __('dashboard.existing_documents') }}</label>

                                        @if ($vehicle->documents->count())
                                            <div class="border rounded p-2">
                                                @foreach ($vehicle->documents as $doc)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                            target="_blank" class="me-2">
                                                            <i class="fas fa-file-alt fa-lg text-secondary"></i>
                                                        </a>

                                                        <small class="me-3 text-muted">
                                                            {{ $doc->start_date ?? '-' }} →
                                                            {{ $doc->end_date ?? '-' }}
                                                        </small>


                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>

                                    <!-- Add New Documents -->
                                    <div class="col-md-12">
                                        <label>{{ __('dashboard.add_new_documents') }}</label>

                                        <div class="vehicle-document-wrapper">

                                            <div class="mb-3 vehicle-document-input border p-2 rounded">
                                                <div class="row no-gutters align-items-center">

                                                    <div class="col-md-4 pr-2">
                                                        <input type="file" class="form-control"
                                                            name="vehicle_files[]">
                                                    </div>

                                                    <div class="col-md-3 pr-2">
                                                        <input type="date" class="form-control"
                                                            name="vehicle_start_date[]">
                                                    </div>

                                                    <div class="col-md-3 pr-2">
                                                        <input type="date" class="form-control"
                                                            name="vehicle_end_date[]">
                                                    </div>

                                                    <div class="col-md-2 text-center">
                                                        <button type="button"
                                                            class="btn btn-success btn-sm edit-add-vehicle-document-btn">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.update') }}
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- Vehicle Delete Modals --}}
        @foreach ($vehicles as $vehicle)
            <div class="modal fade" id="deleteVehicleModal_{{ $vehicle->id }}" tabindex="-1"
                aria-labelledby="deleteVehicleModalLabel_{{ $vehicle->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteVehicleModalLabel_{{ $vehicle->id }}">
                                {{ __('dashboard.delete_vehicle') }}
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.vehicle.destroy', $vehicle->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $vehicle->name }}</strong>?
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
                $('#addVehicleModal').modal('show');
            });
        @endif
    </script>
    <script>
        $(document).on('click', '.add-vehicle-document-btn', function() {
            let html = `
        <div class="mb-3 vehicle-document-input border p-2 rounded">
            <div class="row no-gutters align-items-center">

                <div class="col-md-4 pr-2">
                    <input type="file" class="form-control" name="vehicle_files[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="vehicle_start_date[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="vehicle_end_date[]">
                </div>

                <div class="col-md-2 text-center">
                    <button type="button"
                        class="btn btn-danger btn-sm remove-vehicle-document-btn">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>

                <div class="col-12 mt-1">
                    <span class="text-danger error-text vehicle_file_error"></span>
                </div>

            </div>
        </div>
    `;

            $('#vehicle-document-wrapper').append(html);
        });

        $(document).on('click', '.remove-vehicle-document-btn', function() {
            $(this).closest('.vehicle-document-input').remove();
        });

        // Add new document input dynamically
        $(document).on('click', '.edit-add-vehicle-document-btn', function() {
            let html = `
        <div class="mb-3 vehicle-document-input border p-2 rounded">
            <div class="row no-gutters align-items-center">

                <div class="col-md-4 pr-2">
                    <input type="file" class="form-control" name="vehicle_files[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="vehicle_start_date[]">
                </div>

                <div class="col-md-3 pr-2">
                    <input type="date" class="form-control" name="vehicle_end_date[]">
                </div>

                <div class="col-md-2 text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-vehicle-document-btn">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>

                <div class="col-12 mt-1">
                    <span class="text-danger error-text vehicle_file_error"></span>
                </div>

            </div>
        </div>
    `;

            // Append the new block to the wrapper
            $(this).closest('.vehicle-document-wrapper').append(html);
        });

        // Remove a document input block dynamically
        $(document).on('click', '.remove-vehicle-document-btn', function() {
            $(this).closest('.vehicle-document-input').remove();
        });
    </script>


@endsection
