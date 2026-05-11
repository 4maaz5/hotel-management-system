@extends('layout.master')
@section('title', 'Dashboard | Projects')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_projects') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.projects') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addProjectModal">
                                    {{ __('dashboard.add_project') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.project_name') }}</th>
                                                <th>{{ __('dashboard.location') }}</th>
                                                <th>{{ __('dashboard.project_manager') }}</th>
                                                <th>{{ __('dashboard.value') }}</th>
                                                <th>{{ __('dashboard.timeline_type') }}</th>
                                                <th>{{ __('dashboard.start_date') }}</th>
                                                <th>{{ __('dashboard.end_date') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($projects as $project)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $project->name }}</td>
                                                    <td>{{ $project->location ?? '-' }}</td>
                                                    <td>{{ $project->project_manager ?? '-' }}</td>
                                                    <td>{{ $project->value ? number_format($project->value, 2) : '-' }}
                                                    </td>
                                                    <td>{{ ucfirst($project->timeline_type) }}</td>
                                                    <td>{{ $project->start_date ?? '-' }}</td>
                                                    <td>{{ $project->end_date ?? '-' }}</td>
                                                    <td>
                                                        @if ($project->documents)
                                                            <div class="d-flex flex-wrap">
                                                                @foreach (json_decode($project->documents) as $doc)
                                                                    <div class="me-2 mb-1">
                                                                        <a href="{{ asset('storage/' . $doc) }}"
                                                                            target="_blank" title="{{ basename($doc) }}">
                                                                            <i
                                                                                class="fas fa-file-pdf fa-lg text-danger"></i>
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
                                                            data-target="#editProjectModal{{ $project->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteProjectModal_{{ $project->id }}">
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

        <!-- Add Project Modal (Bootstrap 4) -->
        <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('dashboard.company.project.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.add_project') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <!-- Project Name -->
                                <div class="form-group col-md-6">
                                    <label for="project_name">{{ __('dashboard.project_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="project_name" class="form-control"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="form-group col-md-6">
                                    <label for="project_location">{{ __('dashboard.location') }}</label>
                                    <input type="text" name="location" id="project_location" class="form-control"
                                        value="{{ old('location') }}">
                                    @error('location')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Project Manager -->
                                <div class="form-group col-md-6">
                                    <label for="project_manager">{{ __('dashboard.project_manager') }}</label>
                                    <input type="text" name="project_manager" id="project_manager" class="form-control"
                                        value="{{ old('project_manager') }}">
                                    @error('project_manager')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Project Value -->
                                <div class="form-group col-md-6">
                                    <label for="project_value">{{ __('dashboard.project_value') }}</label>
                                    <input type="number" name="value" id="project_value" class="form-control"
                                        step="0.01" value="{{ old('value') }}">
                                    @error('value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Documents -->
                                <div class="form-group col-md-6">
                                    <label for="documents">{{ __('dashboard.documents') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="documents[]" id="documents" class="custom-file-input"
                                            multiple>
                                        <label class="custom-file-label"
                                            for="documents">{{ __('dashboard.choose_files') }}</label>
                                    </div>
                                    @error('documents')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>




                                <!-- Timeline Type -->
                                <div class="form-group col-md-6">
                                    <label for="timeline_type">{{ __('dashboard.timeline_type') }}</label>
                                    <select name="timeline_type" id="timeline_type" class="form-control">
                                        <option value="fixed" {{ old('timeline_type') == 'fixed' ? 'selected' : '' }}>
                                            {{ __('dashboard.fixed') }}</option>
                                        <option value="milestone"
                                            {{ old('timeline_type') == 'milestone' ? 'selected' : '' }}>
                                            {{ __('dashboard.milestone') }}</option>
                                    </select>
                                    @error('timeline_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Start Date -->
                                <div class="form-group col-md-6">
                                    <label for="start_date">{{ __('dashboard.start_date') }}</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="form-group col-md-6">
                                    <label for="end_date">{{ __('dashboard.end_date') }}</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <!-- Edit Project Modal -->
        @foreach ($projects as $project)
            <div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('dashboard.company.project.update', $project->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_project') }} - {{ $project->name }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-row">
                                    <!-- Project Name -->
                                    <div class="form-group col-md-6">
                                        <label for="project_name_{{ $project->id }}">{{ __('dashboard.project_name') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="project_name_{{ $project->id }}"
                                            class="form-control" value="{{ old('name', $project->name) }}" required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Location -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="project_location_{{ $project->id }}">{{ __('dashboard.location') }}</label>
                                        <input type="text" name="location" id="project_location_{{ $project->id }}"
                                            class="form-control" value="{{ old('location', $project->location) }}">
                                        @error('location')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Project Manager -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="project_manager_{{ $project->id }}">{{ __('dashboard.project_manager') }}</label>
                                        <input type="text" name="project_manager"
                                            id="project_manager_{{ $project->id }}" class="form-control"
                                            value="{{ old('project_manager', $project->project_manager) }}">
                                        @error('project_manager')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Project Value -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="project_value_{{ $project->id }}">{{ __('dashboard.project_value') }}</label>
                                        <input type="number" name="value" id="project_value_{{ $project->id }}"
                                            class="form-control" step="0.01"
                                            value="{{ old('value', $project->value) }}">
                                        @error('value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Documents -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="documents_{{ $project->id }}">{{ __('dashboard.documents') }}</label>
                                        <div class="custom-file">
                                            <input type="file" name="documents[]" id="documents_{{ $project->id }}"
                                                class="custom-file-input" multiple>
                                            <label class="custom-file-label"
                                                for="documents_{{ $project->id }}">{{ __('dashboard.choose_files') }}</label>
                                        </div>
                                        @error('documents')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!-- Existing Documents -->
                                        @if ($project->documents)
                                            <div class="mt-2">
                                                @foreach (json_decode($project->documents) as $doc)
                                                    <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                                        class="d-block">
                                                        <i class="fas fa-file-pdf text-danger"></i> {{ basename($doc) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Timeline Type -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="timeline_type_{{ $project->id }}">{{ __('dashboard.timeline_type') }}</label>
                                        <select name="timeline_type" id="timeline_type_{{ $project->id }}"
                                            class="form-control">
                                            <option value="fixed"
                                                {{ old('timeline_type', $project->timeline_type) == 'fixed' ? 'selected' : '' }}>
                                                Fixed</option>
                                            <option value="milestone"
                                                {{ old('timeline_type', $project->timeline_type) == 'milestone' ? 'selected' : '' }}>
                                                Milestone</option>
                                        </select>
                                        @error('timeline_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Start Date -->
                                    <div class="form-group col-md-6">
                                        <label
                                            for="start_date_{{ $project->id }}">{{ __('dashboard.start_date') }}</label>
                                        <input type="date" name="start_date" id="start_date_{{ $project->id }}"
                                            class="form-control" value="{{ old('start_date', $project->start_date) }}">
                                        @error('start_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- End Date -->
                                    <div class="form-group col-md-6">
                                        <label for="end_date_{{ $project->id }}">{{ __('dashboard.end_date') }}</label>
                                        <input type="date" name="end_date" id="end_date_{{ $project->id }}"
                                            class="form-control" value="{{ old('end_date', $project->end_date) }}">
                                        @error('end_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.close') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach


        {{-- Project Delete Modals --}}
        @foreach ($projects as $project)
            <div class="modal fade" id="deleteProjectModal_{{ $project->id }}" tabindex="-1"
                aria-labelledby="deleteProjectModalLabel_{{ $project->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteProjectModalLabel_{{ $project->id }}">
                                {{ __('dashboard.delete_project') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.project.destroy', $project->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $project->name }}</strong>?
                                </p>
                                @if ($project->documents)
                                    <p class="text-warning">
                                        <small>{{ __('dashboard.all_docs') }}</small>
                                    </p>
                                @endif
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
    <!-- Script to reopen modal -->
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#addProjectModal').modal('show');
            @endif
        });

        $('#documents').on('change', function() {
            var fileNames = Array.from(this.files).map(file => file.name).join(', ');
            $(this).next('.custom-file-label').html(fileNames);
        });
    </script>

@endsection
