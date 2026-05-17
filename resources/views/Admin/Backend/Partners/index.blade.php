@extends('layout.master')
@section('title', 'Dashboard | Partners')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_partners') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.partners') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#partnerModal">
                                    {{ __('dashboard.add_partner') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.partner_name') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.nationality') }}</th>
                                                <th>{{ __('dashboard.share') }} %</th>
                                                <th>{{ __('dashboard.share_quantity') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($partners as $partner)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $partner->company->name }}</td>
                                                    <td>{{ $partner->full_name }}</td>
                                                    <td>{{ ucfirst($partner->partner_type) }}</td>
                                                    <td>{{ $partner->email ?? '-' }}</td>
                                                    <td>{{ $partner->phone ?? '-' }}</td>
                                                    <td>{{ $partner->nationality }}</td>
                                                    <td>{{ $partner->share_percentage }}</td>
                                                    <td>{{ $partner->share_quantity }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            data-toggle="modal"
                                                            data-target="#documentsModal{{ $partner->id }}">
                                                            {{ __('dashboard.view_docs') }}
                                                            ({{ $partner->documents->count() }})
                                                        </button>
                                                    </td>
                                                    <td>


                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editPartnerModal__{{ $partner->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>



                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deletePartnerModal" data-id="{{ $partner->id }}"
                                                            data-name="{{ $partner->full_name }}" data-toggle="modal"
                                                            data-target="#deletePartnerModal">
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

        <!-- Add / Edit Company Partner Modal -->
        <div class="modal fade" id="partnerModal" tabindex="-1" aria-labelledby="partnerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="partnerModalLabel">
                            {{ __('dashboard.add_partner') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="partnerForm" method="POST" action="{{ route('dashboard.finance.partner.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- For Edit -->


                            <div class="row">
                                <!-- Full Name -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.full_name') }}</label>
                                    <input type="text" name="full_name" class="form-control">
                                </div>
                                <!-- Email -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.email') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="email" name="email" class="form-control">
                                </div>

                                <!-- Phone -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.phone') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <!-- Nationality -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.nationality') }}</label>
                                    <input type="text" name="nationality" class="form-control" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.company') }}</label>
                                    <select name="company_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Partner Type -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.partner_type') }}</label>
                                    <select name="partner_type" class="form-control" required>
                                        <option value="">{{ __('dashboard.type') }}</option>
                                        <option value="owner">{{ __('dashboard.owner') }}</option>
                                        <option value="investor">{{ __('dashboard.investor') }}</option>
                                    </select>
                                </div>

                                <!-- ID Type -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.id_type') }}</label>
                                    <select name="id_type" class="form-control" required>
                                        <option value="">{{ __('dashboard.select') }}</option>
                                        <option value="national_id">{{ __('dashboard.national_id') }}</option>
                                        <option value="iqama">{{ __('dashboard.iqama') }}</option>
                                        <option value="passport">{{ __('dashboard.passport') }}</option>
                                    </select>
                                </div>

                                <!-- ID Number -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.id_number') }}</label>
                                    <input type="text" name="id_number" class="form-control" required>
                                </div>

                                <!-- Investment Amount -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.investment_amount') }}</label>
                                    <input type="number" step="0.01" name="investment_amount" class="form-control">
                                </div>

                                <!-- Share Percentage -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.share_percentage') }}</label>
                                    <input type="number" step="0.01" name="share_percentage" class="form-control">
                                </div>

                                <!-- Share Quantity -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.share_quantity') }}</label>
                                    <input type="number" name="share_quantity" class="form-control">
                                </div>
                                <!-- Notes -->
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.documents') }}</label>

                                    <div id="documents-wrapper">

                                        <div class="row document-row mb-2">
                                            <div class="col-md-4">
                                                <select name="documents[0][type]" class="form-control" required>
                                                    <option value="">{{ __('dashboard.select_document_type') }}
                                                    </option>
                                                    <option value="passport">{{ __('dashboard.passport') }}</option>
                                                    <option value="iqama">{{ __('dashboard.iqama') }}</option>
                                                    <option value="national_id">{{ __('dashboard.national_id') }}</option>
                                                    <option value="agreement">{{ __('dashboard.agreement') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <input type="file" name="documents[0][file]" class="form-control"
                                                    required>
                                            </div>

                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-doc d-none">−</button>
                                            </div>
                                        </div>

                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-document">
                                        + {{ __('dashboard.add_document') }}
                                    </button>
                                </div>

                            </div>

                            <!-- Footer Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary">
                                    {{ __('dashboard.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.save') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Company Partner Modal -->
        @foreach ($partners as $partner)
            <div class="modal fade" id="editPartnerModal__{{ $partner->id }}" tabindex="-1"
                aria-labelledby="editPartnerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPartnerModalLabel">
                                {{ __('dashboard.edit_partner') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <form id="partnerForm" method="POST"
                                action="{{ route('dashboard.finance.partner.update', $partner->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">

                                    <!-- Full Name -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.full_name') }}</label>
                                        <input type="text" name="full_name" class="form-control"
                                            value="{{ $partner->full_name }}">
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.email') }} ({{ __('dashboard.optional') }})</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $partner->email }}">
                                    </div>

                                    <!-- Phone -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.phone') }} ({{ __('dashboard.optional') }})</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $partner->phone }}">
                                    </div>

                                    <!-- Nationality -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.nationality') }}</label>
                                        <input type="text" name="nationality" class="form-control" required
                                            value="{{ $partner->nationality }}">
                                    </div>

                                    <!-- Company -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.company') }}</label>
                                        <select name="company_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_company') }}</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ $partner->company_id == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Partner Type -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.partner_type') }}</label>
                                        <select name="partner_type" class="form-control" required>
                                            <option value="">{{ __('dashboard.select') }}</option>
                                            <option value="owner"
                                                {{ $partner->partner_type == 'owner' ? 'selected' : '' }}>
                                                {{ __('dashboard.owner') }}
                                            </option>
                                            <option value="investor"
                                                {{ $partner->partner_type == 'investor' ? 'selected' : '' }}>
                                                {{ __('dashboard.investor') }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- ID Type -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.id_type') }}</label>
                                        <select name="id_type" class="form-control" required>
                                            <option value="">{{ __('dashboard.select') }}</option>
                                            <option value="national_id"
                                                {{ $partner->id_type == 'national_id' ? 'selected' : '' }}>
                                                {{ __('dashboard.national_id') }}
                                            </option>
                                            <option value="iqama" {{ $partner->id_type == 'iqama' ? 'selected' : '' }}>
                                                {{ __('dashboard.iqama') }}
                                            </option>
                                            <option value="passport"
                                                {{ $partner->id_type == 'passport' ? 'selected' : '' }}>
                                                {{ __('dashboard.passport') }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- ID Number -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.id_number') }}</label>
                                        <input type="text" name="id_number" class="form-control" required
                                            value="{{ $partner->id_number }}">
                                    </div>

                                    <!-- Investment Amount -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.investment_amount') }}</label>
                                        <input type="number" step="0.01" name="investment_amount"
                                            class="form-control" value="{{ $partner->investment_amount }}">
                                    </div>

                                    <!-- Share Percentage -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.share_percentage') }}</label>
                                        <input type="number" step="0.01" name="share_percentage"
                                            class="form-control" value="{{ $partner->share_percentage }}">
                                    </div>

                                    <!-- Share Quantity -->
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.share_quantity') }}</label>
                                        <input type="number" name="share_quantity" class="form-control"
                                            value="{{ $partner->share_quantity }}">
                                    </div>

                                    <!-- Notes -->
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.notes') }}</label>
                                        <textarea name="notes" class="form-control" rows="3">{{ $partner->notes }}</textarea>
                                    </div>

                                    <!-- Documents -->
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.documents') }}</label>

                                        <!-- Existing documents -->
                                        <ul class="list-group mb-2">
                                            @foreach ($partner->documents as $doc)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>{{ ucfirst($doc->document_type) }} -
                                                        {{ $doc->original_name }}</span>
                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                                        class="btn btn-sm btn-primary">{{ __('dashboard.view') }}</a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <!-- Add new documents -->
                                        <div id="documents-wrapper">
                                            <div class="row document-row mb-2">
                                                <div class="col-md-4">
                                                    <select name="documents[0][type]" class="form-control">
                                                        <option value="">{{ __('dashboard.select_document_type') }}
                                                        </option>
                                                        <option value="passport">{{ __('dashboard.passport') }}</option>
                                                        <option value="iqama">{{ __('dashboard.iqama') }}</option>
                                                        <option value="national_id">{{ __('dashboard.national_id') }}
                                                        </option>
                                                        <option value="agreement">{{ __('dashboard.agreement') }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="file" name="documents[0][file]" class="form-control">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button"
                                                        class="btn btn-danger remove-doc d-none">−</button>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                                <!-- Footer Buttons -->
                                <div class="text-end mt-3">
                                    <button type="reset"
                                        class="btn btn-secondary">{{ __('dashboard.reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                                </div>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        <!-- Delete Partner Modal -->
        @foreach ($partners as $partner)
            <div class="modal fade" id="deletePartnerModal" tabindex="-1" aria-labelledby="deletePartnerModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deletePartnerModalLabel">
                                {{ __('dashboard.delete_partner') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form id="deletePartnerForm" method="POST"
                            action="{{ route('dashboard.finance.partner.delete') }}">
                            @csrf
                            @method('DELETE')

                            <input type="hidden" name="id" id="delete_partner_id" value="{{ $partner->id }}">

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong id="delete_partner_name"></strong>?
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


        @foreach ($partners as $partner)
            <div class="modal fade" id="documentsModal{{ $partner->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.documents') }} - {{ $partner->full_name }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            @if ($partner->documents->count() > 0)
                                <ul class="list-group">
                                    @foreach ($partner->documents as $doc)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ ucfirst($doc->document_type) }} - {{ $doc->original_name }}</span>
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                {{ __('dashboard.view_download') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>{{ __('dashboard.no_documents_uploaded') }}</p>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.close') }}</button>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach


    </div>

    <script>
        let docIndex = 1;

        document.getElementById('add-document').addEventListener('click', function() {
            const wrapper = document.getElementById('documents-wrapper');

            const row = `
    <div class="row document-row mb-2">
        <div class="col-md-4">
            <select name="documents[${docIndex}][type]" class="form-control" required>
                <option value="">{{ __('dashboard.select_document_type') }}</option>
                <option value="passport">{{ __('dashboard.passport') }}</option>
                <option value="iqama">{{ __('dashboard.iqama') }}</option>
                <option value="national_id">{{ __('dashboard.national_id') }}</option>
                <option value="agreement">{{ __('dashboard.agreement') }}</option>
            </select>
        </div>

        <div class="col-md-6">
            <input type="file" name="documents[${docIndex}][file]" class="form-control" required>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-doc">−</button>
        </div>
    </div>`;

            wrapper.insertAdjacentHTML('beforeend', row);
            docIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-doc')) {
                e.target.closest('.document-row').remove();
            }
        });
    </script>

@endsection
