@extends('layout.master')
@section('title', 'Dashboard | Projects-Executive')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_executives') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.executives') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addProjectExecutiveModal">
                                    {{ __('dashboard.add_executive') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.project') }}</th>
                                                <th>{{ __('dashboard.location') }}</th>
                                                <th>{{ __('dashboard.responsible_person_name') }}</th>
                                                <th>{{ __('dashboard.company') }}</th>
                                                <th>{{ __('dashboard.contract_reference') }}</th>
                                                <th>{{ __('dashboard.timeline') }}</th>
                                                <th>{{ __('dashboard.start_date') }}</th>
                                                <th>{{ __('dashboard.end_date') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($executives as $executive)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Project Name -->
                                                    <td>{{ $executive->project->name ?? '-' }}</td>

                                                    <!-- Project Location -->
                                                    <td>{{ $executive->project->location ?? '-' }}</td>

                                                    <!-- Responsible Person -->
                                                    <td>{{ $executive->responsible_person_name }}</td>

                                                    <!-- Company -->
                                                    <td>{{ $executive->company_name ?? '-' }}</td>

                                                    <!-- Contract Reference -->
                                                    <td>{{ $executive->contract_reference ?? '-' }}</td>

                                                    <!-- Timeline -->
                                                    <td>{{ ucfirst($executive->project->timeline_type ?? '-') }}</td>

                                                    <!-- Dates -->
                                                    <td>{{ $executive->project->start_date ?? '-' }}</td>
                                                    <td>{{ $executive->project->end_date ?? '-' }}</td>

                                                    <!-- Documents -->
                                                    <td>
                                                        @php
                                                            $projectDocuments = $executive->project
                                                                ? (is_array($executive->project->documents)
                                                                    ? $executive->project->documents
                                                                    : json_decode($executive->project->documents ?? '[]', true))
                                                                : [];
                                                            $projectDocuments = is_array($projectDocuments)
                                                                ? array_filter($projectDocuments)
                                                                : [];
                                                        @endphp
                                                        @if (count($projectDocuments))
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($projectDocuments as $doc)
                                                                    <a href="{{ asset('storage/' . $doc) }}"
                                                                        target="_blank" class="mr-2"
                                                                        title="{{ basename($doc) }}">
                                                                        <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <!-- Actions -->
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editProjectExecutiveModal{{ $executive->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteProjectExecutiveModal_{{ $executive->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
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

        <!-- Add Project Executive Modal -->
        <div class="modal fade" id="addProjectExecutiveModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <form action="{{ route('dashboard.company.executive.store') }}" method="POST">
                        @csrf

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.add_project_executive') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">

                            <!-- Row 1 -->
                            <div class="form-row">
                                <!-- Project -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.project') }} <span class="text-danger">*</span></label>
                                    <select name="project_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_project') }}</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Responsible Person -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.responsible_person_name') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="responsible_person_name" class="form-control"
                                        value="{{ old('responsible_person_name') }}" required>
                                    @error('responsible_person_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="form-row">
                                <!-- Contract Reference -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.contract_reference') }}</label>
                                    <input type="text" name="contract_reference" class="form-control"
                                        value="{{ old('contract_reference') }}">
                                    @error('contract_reference')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Company Name -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_name') }}</label>
                                    <input type="text" name="company_name" class="form-control"
                                        value="{{ old('company_name') }}">
                                    @error('company_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>


        @foreach ($executives as $executive)
            <div class="modal fade" id="editProjectExecutiveModal{{ $executive->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">

                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <form action="{{ route('dashboard.company.executive.update', $executive->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ __('dashboard.edit_project_executive') }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body">

                                <!-- Row 1 -->
                                <div class="form-row">
                                    <!-- Project -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.project') }}
                                            <span class="text-danger">*</span></label>
                                        <select name="project_id" class="form-control" required>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    {{ old('project_id', $executive->project_id) == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Responsible Person -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.responsible_person_name') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" name="responsible_person_name" class="form-control"
                                            value="{{ old('responsible_person_name', $executive->responsible_person_name) }}"
                                            required>
                                        @error('responsible_person_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 2 -->
                                <div class="form-row">
                                    <!-- Contract Reference -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.contract_reference') }}</label>
                                        <input type="text" name="contract_reference" class="form-control"
                                            value="{{ old('contract_reference', $executive->contract_reference) }}">
                                        @error('contract_reference')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Company Name -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.company_name') }}</label>
                                        <input type="text" name="company_name" class="form-control"
                                            value="{{ old('company_name', $executive->company_name) }}">
                                        @error('company_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
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

                        </form>

                    </div>
                </div>
            </div>
        @endforeach



        {{-- Project Executive Delete Modals --}}
        @foreach ($executives as $executive)
            <div class="modal fade" id="deleteProjectExecutiveModal_{{ $executive->id }}" tabindex="-1"
                aria-labelledby="deleteProjectExecutiveModalLabel_{{ $executive->id }}" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteProjectExecutiveModalLabel_{{ $executive->id }}">
                                {{ __('dashboard.delete_project_executive') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.executive.destroy', $executive->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>
                                    {{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $executive->responsible_person_name }}</strong>?
                                </p>

                                <p class="text-muted">
                                    <small>
                                        {{ __('dashboard.project') }}:
                                        <strong>{{ $executive->project->name ?? '-' }}</strong>
                                    </small>
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
