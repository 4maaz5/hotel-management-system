@extends('layout.master')
@section('title', 'Dashboard | Contracts')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_contracts') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.contracts') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#contractModal">
                                    {{ __('dashboard.add_contract') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('dashboard.contract_number') }}</th>
                                                <th>{{ __('dashboard.title') }}</th>
                                                <th>{{ __('dashboard.client_company') }}</th>
                                                <th>{{ __('dashboard.contract_company') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.start_date') }}</th>
                                                <th>{{ __('dashboard.end_date') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($contracts as $contract)
                                                <tr>
                                                    <td>{{ $contract->contract_number }}</td>
                                                    <td>{{ $contract->title }}</td>
                                                    <td>{{ $contract->client->company_name }}</td>
                                                    <td>{{ $contract->company->legal_name }}</td>
                                                    <td>{{ ucfirst($contract->status) }}</td>
                                                    <td>{{ $contract->start_date->format('d-m-Y') }}</td>
                                                    <td>{{ $contract->end_date->format('d-m-Y') }}</td>

                                                    <!-- Documents -->
                                                    <td>
                                                        @if ($contract->file)
                                                            <div class="d-flex flex-wrap">
                                                                @foreach (json_decode($contract->file) as $file)
                                                                    <div class="me-2 mb-1">
                                                                        <a href="{{ asset('storage/' . $file) }}"
                                                                            target="_blank">
                                                                            <i
                                                                                class="fas fa-file-alt fa-lg text-secondary"></i>
                                                                            {{-- {{ basename($file) }} --}}
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <!-- Actions -->
                                                    <td>

                                                        <a href="{{ route('dashboard.company.contract.print', $contract->id) }}"
                                                            class="text-secondary" target="_blank"> <i
                                                                class="fas fa-print"></i></a>

                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editContractModal{{ $contract->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteContractModal_{{ $contract->id }}">
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


        <!-- Add / Edit Contract Modal -->
        <div class="modal fade" id="contractModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Form -->
                    <form action="{{ route('dashboard.company.contract.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden for Edit -->
                        <input type="hidden" name="contract_id" id="contract_id">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="contractModalTitle">{{ __('dashboard.add_contract') }}</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <div class="form-row">

                                <!-- Client -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.client') }} <span class="text-danger">*</span></label>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_client') }}</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- company -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company') }} <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Contract Title -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.contract_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-row">
                                <!-- Contract Number -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.contract_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="contract_number" id="contract_number" class="form-control"
                                        required>
                                    @error('contract_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- File Upload -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.contract_file') }}</label>
                                    <div class="custom-file">
                                        <input type="file" name="files[]" id="file" class="custom-file-input"
                                            multiple>
                                        <label class="custom-file-label"
                                            for="file">{{ __('dashboard.choose_files') }}</label>
                                    </div>
                                    @error('files')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-row">
                                <!-- Status -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active">{{ __('dashboard.active') }}</option>
                                        <option value="near_expiry">{{ __('dashboard.near_expiry') }}</option>
                                        <option value="expired">{{ __('dashboard.expired') }}</option>
                                        <option value="ended">{{ __('dashboard.ended') }}</option>
                                    </select>
                                </div>

                                <!-- Start Date -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.start_date') }}</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                </div>

                                <!-- End Date -->
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.end_date') }}</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label>{{ __('dashboard.remarks') }}</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                                @error('remarks')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary"
                                id="contractModalBtn">{{ __('dashboard.save') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>


        @foreach ($contracts as $contract)
            <!-- Edit Contract Modal -->
            <div class="modal fade" id="editContractModal{{ $contract->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <!-- Form -->
                        <form action="{{ route('dashboard.company.contract.update', $contract->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Important for update -->

                            <input type="hidden" name="edit_contract_id" value="{{ $contract->id }}">

                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_contract') }}: {{ $contract->title }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body">
                                <div class="form-row">

                                    <!-- Client -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.client') }} <span class="text-danger">*</span></label>
                                        <select name="client_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_client') }}</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ $contract->client_id == $client->id ? 'selected' : '' }}>
                                                    {{ $client->client_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Company -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.contract_company') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="company_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_company') }}</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ $contract->company_id == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Contract Title -->
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.contract_title') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $contract->title }}" required>
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="form-row">
                                    <!-- Contract Number -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.contract_number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="contract_number" class="form-control"
                                            value="{{ $contract->contract_number }}" required>
                                        @error('contract_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- File Upload -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.contract_file') }}</label>
                                        <div class="custom-file">
                                            <input type="file" name="files[]" class="custom-file-input" multiple>
                                            <label class="custom-file-label"
                                                for="files">{{ __('dashboard.choose_files') }}</label>
                                        </div>

                                        @if ($contract->file)
                                            <div class="mt-2">
                                                <strong>{{ __('dashboard.all_doc') }}:</strong>
                                                <div class="d-flex flex-wrap mt-1">
                                                    @foreach (json_decode($contract->file) as $file)
                                                        <div class="me-2 mb-1">
                                                            <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                                <i class="fas fa-file-alt text-secondary"></i>
                                                                {{-- {{ basename($file) }} --}}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @error('files')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Status -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.status') }}</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ $contract->status == 'active' ? 'selected' : '' }}>
                                                {{ __('dashboard.active') }}</option>
                                            <option value="near_expiry"
                                                {{ $contract->status == 'near_expiry' ? 'selected' : '' }}>
                                                {{ __('dashboard.near_expiry') }}
                                            </option>
                                            <option value="expired"
                                                {{ $contract->status == 'expired' ? 'selected' : '' }}>
                                                {{ __('dashboard.expired') }}</option>
                                            <option value="ended" {{ $contract->status == 'ended' ? 'selected' : '' }}>
                                                {{ __('dashboard.ended') }}</option>
                                        </select>
                                    </div>

                                    <!-- Start Date -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.start_date') }}</label>
                                        <input type="date" name="start_date" class="form-control"
                                            value="{{ $contract->start_date->format('Y-m-d') }}">
                                    </div>

                                    <!-- End Date -->
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.end_date') }}</label>
                                        <input type="date" name="end_date" class="form-control"
                                            value="{{ $contract->end_date->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div class="form-group">
                                    <label>{{ __('dashboard.remarks') }}</label>
                                    <textarea name="remarks" class="form-control" rows="3">{{ $contract->remarks }}</textarea>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        @endforeach


        {{-- Contract Delete Modals --}}
        @foreach ($contracts as $contract)
            <div class="modal fade" id="deleteContractModal_{{ $contract->id }}" tabindex="-1"
                aria-labelledby="deleteContractModalLabel_{{ $contract->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteContractModalLabel_{{ $contract->id }}">
                                {{ __('dashboard.delete_contract') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.contract.destroy', $contract->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>
                                    {{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $contract->title }}</strong>?
                                </p>

                                <small class="text-danger d-block mt-2">
                                    {{ __('dashboard.all_docs_will_be_deleted') }}
                                </small>
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


    @if ($errors->any() && !old('edit_contract_id'))
        <script>
            $(document).ready(function() {
                $('#contractModal').modal('show');
            });
        </script>
    @endif
    @if ($errors->any() && old('edit_contract_id'))
        <script>
            $(document).ready(function() {
                $('#editContractModal' + '{{ old('edit_contract_id') }}').modal('show');
            });
        </script>
    @endif


@endsection
