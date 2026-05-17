@extends('layout.master')
@section('title', 'Dashboard | Warehouse')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.letter_setting') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.letter_setting') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#letterSettingsModal">
                                    {{ __('dashboard.add_setting') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name_ar') }}</th>
                                                <th>{{ __('dashboard.company_logo') }}</th>
                                                <th>{{ __('dashboard.authorized_sign_name') }}</th>
                                                <th>{{ __('dashboard.authorized_sign_title') }}</th>
                                                <th>{{ __('dashboard.signature_image') }}</th>
                                                <th>{{ __('dashboard.stamp_image') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if ($letterSettings->count())
                                                @foreach ($letterSettings as $index => $setting)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>

                                                        <td>{{ $setting->company_name_ar }}</td>

                                                        <!-- Company Logo -->
                                                        <td>
                                                            @if ($setting->company_logo)
                                                                <img src="{{ asset('storage/' . $setting->company_logo) }}"
                                                                    alt="Logo" width="50">
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        <td>{{ $setting->authorized_sign_name }}</td>
                                                        <td>{{ $setting->authorized_sign_title }}</td>

                                                        <!-- Signature Image -->
                                                        <td>
                                                            @if ($setting->signature_image)
                                                                <img src="{{ asset('storage/' . $setting->signature_image) }}"
                                                                    alt="Signature" width="50">
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        <!-- Stamp Image -->
                                                        <td>
                                                            @if ($setting->stamp_image)
                                                                <img src="{{ asset('storage/' . $setting->stamp_image) }}"
                                                                    alt="Stamp" width="50">
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <!-- Edit -->
                                                            <a href="#" class="text-secondary" data-toggle="modal"
                                                                data-target="#editLetterSettingModal_{{ $setting->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <!-- Delete -->
                                                            <a href="#" class="text-danger" data-toggle="modal"
                                                                data-target="#deleteLetterModal_{{ $setting->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        {{ __('dashboard.no_settings_found') }}
                                                    </td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Letter Settings Modal -->
        <div class="modal fade" id="letterSettingsModal" tabindex="-1" aria-labelledby="letterSettingsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="letterSettingsModalLabel">
                            {{ __('dashboard.letter_settings') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="letterSettingsForm" method="POST" action="{{ route('dashboard.letter.setting.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_modal_id" value="letterSettingsModal">

                            <div class="row">

                                <!-- Company Name (Arabic) -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_name_ar') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name_ar" class="form-control @error('company_name_ar') is-invalid @enderror" value="{{ old('company_name_ar') }}" required>
                                    @error('company_name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Company Logo -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_logo') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.svg">
                                    <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                    @error('company_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Authorized Sign Name -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.authorized_sign_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="authorized_sign_name" class="form-control @error('authorized_sign_name') is-invalid @enderror" value="{{ old('authorized_sign_name') }}" required>
                                    @error('authorized_sign_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Authorized Sign Title -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.authorized_sign_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="authorized_sign_title" class="form-control @error('authorized_sign_title') is-invalid @enderror" value="{{ old('authorized_sign_title') }}" required>
                                    @error('authorized_sign_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Signature Image -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.signature_image') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="file" name="signature_image" class="form-control @error('signature_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.svg">
                                    <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                    @error('signature_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Stamp Image -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.stamp_image') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="file" name="stamp_image" class="form-control @error('stamp_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.svg">
                                    <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                    @error('stamp_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary">
                                    {{ __('dashboard.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.save_settings') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Letter Edit Settings Modal -->
        @foreach ($letterSettings as $letter)
            <div class="modal fade" id="editLetterSettingModal_{{ $letter->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_letter_setting') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <form method="POST" action="{{ route('dashboard.letter.setting.update', $letter->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <input type="hidden" name="_modal_id" value="editLetterSettingModal_{{ $letter->id }}">
                                <div class="row">

                                    <!-- Company Name Arabic -->
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.company_name_ar') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="company_name_ar" class="form-control @error('company_name_ar') is-invalid @enderror"
                                            value="{{ old('company_name_ar', $letter->company_name_ar) }}" required>
                                        @error('company_name_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Company Logo -->
                                    <div class="form-group col-md-12">
                                        <label>
                                            {{ __('dashboard.company_logo') }}
                                            ({{ __('dashboard.optional') }})
                                        </label>

                                        @if ($letter->company_logo)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $letter->company_logo) }}"
                                                    style="height:60px">
                                            </div>
                                        @endif

                                        <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.svg">
                                        <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                        @error('company_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <!-- Authorized Sign Name -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.authorized_sign_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="authorized_sign_name" class="form-control @error('authorized_sign_name') is-invalid @enderror"
                                            value="{{ old('authorized_sign_name', $letter->authorized_sign_name) }}"
                                            required>
                                        @error('authorized_sign_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Authorized Sign Title -->
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.authorized_sign_title') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="authorized_sign_title" class="form-control @error('authorized_sign_title') is-invalid @enderror"
                                            value="{{ old('authorized_sign_title', $letter->authorized_sign_title) }}"
                                            required>
                                        @error('authorized_sign_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Signature Image -->
                                    <div class="form-group col-md-6">
                                        <label>
                                            {{ __('dashboard.signature_image') }}
                                            ({{ __('dashboard.optional') }})
                                        </label>

                                        @if ($letter->signature_image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $letter->signature_image) }}"
                                                    style="height:60px">
                                            </div>
                                        @endif

                                        <input type="file" name="signature_image" class="form-control @error('signature_image') is-invalid @enderror"
                                            accept=".jpg,.jpeg,.png,.svg">
                                        <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                        @error('signature_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Stamp Image -->
                                    <div class="form-group col-md-6">
                                        <label>
                                            {{ __('dashboard.stamp_image') }}
                                            ({{ __('dashboard.optional') }})
                                        </label>

                                        @if ($letter->stamp_image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $letter->stamp_image) }}"
                                                    style="height:60px">
                                            </div>
                                        @endif

                                        <input type="file" name="stamp_image" class="form-control @error('stamp_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.svg">
                                        <small class="text-muted">{{ __('validation.max.file', ['attribute' => '', 'max' => '2MB']) }}</small>
                                        @error('stamp_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Footer -->
                                <div class="text-end mt-4">
                                    <button type="reset" class="btn btn-secondary">
                                        {{ __('dashboard.reset') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('dashboard.update_setting') }}
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach



        @foreach ($letterSettings as $letter)
            <div class="modal fade" id="deleteLetterModal_{{ $letter->id }}" tabindex="-1"
                aria-labelledby="deleteLetterModalLabel_{{ $letter->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteLetterModalLabel_{{ $letter->id }}">
                                {{ __('dashboard.delete_letter_setting') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.letter.setting.delete', $letter->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $letter->letter_number }}</strong>?
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            var modalId = '{{ old('_modal_id') }}';
            if (modalId) {
                $('#' + modalId).modal('show');
            } else {
                @php
                    $createFields = ['company_name_ar', 'company_logo', 'authorized_sign_name', 'authorized_sign_title', 'signature_image', 'stamp_image'];
                    $hasCreateErrors = collect($createFields)->contains(fn($f) => $errors->has($f));
                @endphp
                @if ($hasCreateErrors)
                    $('#letterSettingsModal').modal('show');
                @endif
            }
        @endif
    });
</script>
@endpush

@endsection
