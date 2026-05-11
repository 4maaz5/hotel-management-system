@extends('layout.master')
@section('title', 'Dashboard | Client')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_clients') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.clients') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addClientModal">
                                    {{ __('dashboard.add_client') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.client_name') }}</th>
                                                <th>{{ __('dashboard.contact_person') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.address') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($clients as $client)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $client->company_name }}</td>
                                                    <td>{{ $client->client_name }}</td>
                                                    <td>{{ $client->contact ?? ($client->person_name ?? '-') }}</td>
                                                    <td>{{ $client->email ?? '-' }}</td>
                                                    <td>{{ $client->phone ?? '-' }}</td>
                                                    <td>{{ $client->address ?? '-' }}</td>
                                                    <td>
                                                        @if ($client->documents->count())
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($client->documents as $doc)
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
                                                            data-target="#editClientModal{{ $client->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteClientModal_{{ $client->id }}">
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


        <!-- Add Client Modal -->
        <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addMarketingAgentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('dashboard.company.client.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title" id="addMarketingAgentModalLabel">
                                {{ __('dashboard.add_client') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_company_name') }}</label>
                                    <input type="text" name="company_name" class="form-control" required>
                                </div>

                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.client_name') }}</label>
                                    <input type="text" name="client_name" class="form-control" required>
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.cr_number') }}</label>
                                    <input type="number" name="cr_number" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.vat_number') }}</label>
                                    <input type="number" name="vat_number" class="form-control">
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }}</label>
                                    <input type="email" name="email" class="form-control">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>

                                <!-- Commission Percentage -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.responsible_person_name') }} </label>
                                    <input type="text" name="person_name" class="form-control">
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.contact_person') }}</label>
                                    <input type="text" name="contact" class="form-control">
                                </div>

                                <!-- Documents + Dates Section -->
                                <div class="col-md-12">
                                    <label>{{ __('dashboard.documents') }}</label>
                                    <div id="edit-document-wrapper">
                                        <!-- First Document Block -->
                                        <div class="mb-3 document-input border p-2 rounded">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-4">
                                                    <input type="file" class="form-control" name="files[]">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" class="form-control" name="start_date[]">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" class="form-control" name="end_date[]">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button"
                                                        class="btn btn-success btn-sm edit-document-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="col-12 mt-1">
                                                    <span class="text-danger error-text file_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label">{{ __('dashboard.address') }}</label>
                                    <textarea name="address" rows="3" class="form-control"></textarea>
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

                    </form>

                </div>
            </div>
        </div>

        {{-- Client Edit Modals --}}
        @foreach ($clients as $client)
            <div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1"
                aria-labelledby="editClientModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <form action="{{ route('dashboard.company.client.update', $client->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title" id="editClientModalLabel">{{ __('dashboard.edit_client') }}</h5>
                                <button type="button" class="close text-dark"
                                    data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    <!-- Company Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_company_name') }}</label>
                                        <input type="text" name="company_name" class="form-control"
                                            value="{{ $client->company_name }}" required>
                                    </div>

                                    <!-- Client Name -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.client_name') }}</label>
                                        <input type="text" name="client_name" class="form-control"
                                            value="{{ $client->client_name }}" required>
                                    </div>

                                    <!-- CR Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.cr_number') }}</label>
                                        <input type="number" name="cr_number" class="form-control"
                                            value="{{ $client->cr_number }}">
                                    </div>

                                    <!-- VAT Number -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.vat_number') }}</label>
                                        <input type="number" name="vat_number" class="form-control"
                                            value="{{ $client->vat_number }}">
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $client->email }}">
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $client->phone }}">
                                    </div>

                                    <!-- Responsible Person -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.responsible_person_name') }}</label>
                                        <input type="text" name="person_name" class="form-control"
                                            value="{{ $client->person_name }}">
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.contact_person') }}</label>
                                        <input type="text" name="contact" class="form-control"
                                            value="{{ $client->contact }}">
                                    </div>

                                    <!-- Documents Section -->
                                    <div class="col-md-12">
                                        <label>{{ __('dashboard.documents') }}</label>
                                        <div id="edit-document-wrapper-{{ $client->id }}">
                                            {{-- Existing documents --}}
                                            @foreach ($client->documents as $doc)
                                                <div class="mb-3 document-input border p-2 rounded">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-md-4">
                                                            <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                                target="_blank">
                                                                {{ \Illuminate\Support\Str::limit(basename($doc->file_path), 25) }}
                                                            </a>
                                                            <input type="file" name="files[]"
                                                                class="form-control mt-1">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="date" name="start_date[]"
                                                                class="form-control" value="{{ $doc->start_date }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="date" name="end_date[]" class="form-control"
                                                                value="{{ $doc->end_date }}">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <button type="button"
                                                                class="btn btn-success btn-sm edit-document-btn"><i
                                                                    class="fas fa-plus"></i></button>
                                                            {{-- <button type="button"
                                                                class="btn btn-danger btn-sm remove-document-btn ms-2"><i
                                                                    class="fas fa-minus"></i></button> --}}
                                                        </div>
                                                        <div class="col-12 mt-1">
                                                            <span class="text-danger error-text file_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Empty block if no documents exist --}}
                                            @if ($client->documents->count() == 0)
                                                <div class="mb-3 document-input border p-2 rounded">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-md-4">
                                                            <input type="file" name="files[]" class="form-control">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="date" name="start_date[]"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="date" name="end_date[]" class="form-control">
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-center">
                                                            <button type="button"
                                                                class="btn btn-success btn-sm edit-document-btn"><i
                                                                    class="fas fa-plus"></i></button>
                                                        </div>
                                                        <div class="col-12 mt-1">
                                                            <span class="text-danger error-text file_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-12">
                                        <label class="form-label">{{ __('dashboard.address') }}</label>
                                        <textarea name="address" rows="3" class="form-control">{{ $client->address }}</textarea>
                                    </div>

                                </div>
                            </div>

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


        {{-- Client Delete Modals --}}
        @foreach ($clients as $client)
            <div class="modal fade" id="deleteClientModal_{{ $client->id }}" tabindex="-1"
                aria-labelledby="deleteClientModalLabel_{{ $client->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteClientModalLabel_{{ $client->id }}">
                                {{ __('dashboard.delete_client') }}
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.client.destroy', $client->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $client->company_name }}</strong>?
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
        document.addEventListener('click', function(e) {

            // ADD new input in Add Client modal
            if (e.target.closest('.add-document-btn')) {
                const wrapper = document.getElementById('document-wrapper');
                if (!wrapper) return;

                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-block');

                newDiv.innerHTML = `
            <div class="form-row mb-2">
                <label>{{ __('dashboard.documents') }}</label>
                <div class="col-md-12 d-flex align-items-center">
                    <input type="file" class="form-control" name="files[]">
                    <button type="button" class="btn btn-danger btn-sm ms-2 remove-document-btn ml-2">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="text-danger error-text file_error ms-2"></span>
                </div>
            </div>
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <label>{{ __('dashboard.start_date') }}</label>
                    <input type="date" name="start_date[]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>{{ __('dashboard.end_date') }}</label>
                    <input type="date" name="end_date[]" class="form-control">
                </div>
            </div>
        `;

                wrapper.appendChild(newDiv);
            }

            // ADD new input in Edit Client modal
            if (e.target.closest('.edit-document-btn')) {
                const btn = e.target.closest('.edit-document-btn');
                const wrapper = btn.closest('.modal-content').querySelector('[id^="edit-document-wrapper"]');
                if (!wrapper) return;

                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-input', 'border', 'p-2', 'rounded');

                newDiv.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="file" class="form-control" name="files[]">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="start_date[]">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="end_date[]">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm remove-document-btn ms-2">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <div class="col-12 mt-1">
                    <span class="text-danger error-text file_error"></span>
                </div>
            </div>
        `;

                wrapper.appendChild(newDiv);
            }

            // REMOVE any document row
            if (e.target.closest('.remove-document-btn')) {
                const parent = e.target.closest('.document-block') || e.target.closest('.document-input');
                if (parent) parent.remove();
            }

        });
    </script>

@endsection
