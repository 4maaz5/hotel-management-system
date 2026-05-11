@extends('layout.master')
@section('title', 'Dashboard | Accidents')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_accidents') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.accidents') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addAccidentModal">
                                    {{ __('dashboard.add_accident') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.vehicle') }}</th>
                                                <th>{{ __('dashboard.driver_name') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.id_number') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.accident_date') }}</th>
                                                <th>{{ __('dashboard.description') }}</th>
                                                <th>{{ __('dashboard.repair_cost') }}</th>
                                                <th>{{ __('dashboard.repair_status') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($accidents as $accident)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Vehicle Info -->
                                                    <td>
                                                        {{ $accident->vehicle->name ?? '-' }}
                                                        ({{ $accident->vehicle->plate_number ?? '-' }})
                                                    </td>

                                                    <!-- Driver Info -->
                                                    <td>{{ $accident->driver->name ?? '-' }}</td>
                                                    <td>{{ $accident->driver->phone ?? '-' }}</td>
                                                    <td>{{ $accident->driver->id_number ?? '-' }}</td>

                                                    <!-- Driver Documents -->
                                                    <td>
                                                        @if ($accident->driver && $accident->driver->documents->count())
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($accident->driver->documents as $doc)
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
                                                    <td>{{ $accident->accident_date }}</td>
                                                    <td>{{ $accident->description }}</td>
                                                    <td>{{ $accident->repair_cost }}</td>
                                                    <td>{{ $accident->repair_status }}</td>
                                                    <!-- Action Buttons -->
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editAccidentModal_{{ $accident->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteAccidentModal_{{ $accident->id }}">
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


        <!-- Add Accident Modal -->
        <div class="modal fade" id="addAccidentModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form action="{{ route('dashboard.company.accident.store') }}" method="POST">
                    @csrf

                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.add_accident') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">

                                <!-- Vehicle -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.vehicle') }}</label>
                                    <select name="vehicle_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_vehicle') }}</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">
                                                {{ $vehicle->plate_number }} - {{ $vehicle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <span class="test-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Driver -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.driver') }}</label>
                                    <select name="driver_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_driver') }}</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">
                                                {{ $driver->name }} ({{ $driver->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')
                                        <span class="test-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Accident Date -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.accident_date') }}</label>
                                    <input type="date" name="accident_date" class="form-control">
                                    @error('accident_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Fine Percentage -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.fine_percentage') }} (%)</label>
                                    <input type="number" name="fine_percentage" class="form-control" step="0.01">
                                </div>

                                <!-- Repair Cost -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.repair_cost') }}</label>
                                    <input type="number" name="repair_cost" class="form-control" step="0.01">
                                </div>

                                <!-- Insurance Coverage -->
                                <div class="col-md-6 mb-3">
                                    <label>{{ __('dashboard.insurance_coverage') }}</label>
                                    <select name="insurance_coverage" class="form-control">
                                        <option value="yes">{{ __('dashboard.yes') }}</option>
                                        <option value="no">{{ __('dashboard.no') }}</option>
                                        <option value="partial">{{ __('dashboard.partial') }}</option>
                                    </select>
                                </div>

                                <!-- Repair Status -->
                                <div class="col-md-12 mb-3">
                                    <label>{{ __('dashboard.repair_status') }}</label>
                                    <select name="repair_status" class="form-control">
                                        <option value="pending">{{ __('dashboard.pending') }}</option>
                                        <option value="in_progress">{{ __('dashboard.in_progress') }}</option>
                                        <option value="completed">{{ __('dashboard.completed') }}</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label>{{ __('dashboard.description') }}</label>
                                    <textarea name="description" rows="3" class="form-control"></textarea>
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

        <!-- Edit Accident Modal -->
        @foreach ($accidents as $accident)
            <div class="modal fade" id="editAccidentModal_{{ $accident->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <form action="{{ route('dashboard.company.accident.update', $accident->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_accident') }}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row">

                                    <!-- Vehicle -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.vehicle') }}</label>
                                        <select name="vehicle_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_vehicle') }}</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $accident->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->plate_number }} - {{ $vehicle->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Driver -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.driver') }}</label>
                                        <select name="driver_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_driver') }}</option>
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}"
                                                    {{ $accident->driver_id == $driver->id ? 'selected' : '' }}>
                                                    {{ $driver->name }} ({{ $driver->phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Accident Date -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.accident_date') }}</label>
                                        <input type="date" name="accident_date" class="form-control"
                                            value="{{ $accident->accident_date }}" required>
                                    </div>

                                    <!-- Fine Percentage -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.fine_percentage') }} (%)</label>
                                        <input type="number" name="fine_percentage" class="form-control" step="0.01"
                                            value="{{ $accident->fine_percentage }}">
                                    </div>

                                    <!-- Repair Cost -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.repair_cost') }}</label>
                                        <input type="number" name="repair_cost" class="form-control" step="0.01"
                                            value="{{ $accident->repair_cost }}">
                                    </div>

                                    <!-- Insurance Coverage -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.insurance_coverage') }}</label>
                                        <select name="insurance_coverage" class="form-control">
                                            <option value="yes"
                                                {{ $accident->insurance_coverage == 'yes' ? 'selected' : '' }}>
                                                {{ __('dashboard.yes') }}</option>
                                            <option value="no"
                                                {{ $accident->insurance_coverage == 'no' ? 'selected' : '' }}>
                                                {{ __('dashboard.no') }}</option>
                                            <option value="partial"
                                                {{ $accident->insurance_coverage == 'partial' ? 'selected' : '' }}>
                                                {{ __('dashboard.partial') }}</option>
                                        </select>
                                    </div>

                                    <!-- Repair Status -->
                                    <div class="col-md-12 mb-3">
                                        <label>{{ __('dashboard.repair_status') }}</label>
                                        <select name="repair_status" class="form-control">
                                            <option value="pending"
                                                {{ $accident->repair_status == 'pending' ? 'selected' : '' }}>
                                                {{ __('dashboard.pending') }}</option>
                                            <option value="in_progress"
                                                {{ $accident->repair_status == 'in_progress' ? 'selected' : '' }}>
                                                {{ __('dashboard.in_progress') }}</option>
                                            <option value="completed"
                                                {{ $accident->repair_status == 'completed' ? 'selected' : '' }}>
                                                {{ __('dashboard.completed') }}</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-md-12 mb-3">
                                        <label>{{ __('dashboard.description') }}</label>
                                        <textarea name="description" rows="3" class="form-control">{{ $accident->description }}</textarea>
                                    </div>

                                </div>
                            </div>

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

        {{-- Accident Delete Modals --}}
        @foreach ($accidents as $accident)
            <div class="modal fade" id="deleteAccidentModal_{{ $accident->id }}" tabindex="-1"
                aria-labelledby="deleteAccidentModalLabel_{{ $accident->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteAccidentModalLabel_{{ $accident->id }}">
                                {{ __('dashboard.delete_accident') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.accident.destroy', $accident->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>
                                        {{ $accident->vehicle->name ?? '-' }}
                                        ({{ $accident->vehicle->plate_number ?? '-' }})
                                        - {{ $accident->driver->name ?? '-' }}
                                    </strong>?
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">
                                    {{ __('dashboard.yes_delete') }}
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>
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
                $('#addAccidentModal').modal('show');
            });
        @endif
    </script>

@endsection
