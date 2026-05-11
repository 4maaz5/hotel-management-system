@extends('layout.master')
@section('title', 'Dashboard | Settings')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.general_setting') }}</h4>
                                @if (empty($setting))
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#editGeneralSettingsModal">
                                        {{ __('dashboard.add_setting') }}
                                    </button>
                                @endif

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.hotel_name') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.logo') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $setting?->hrm_name }}</td>
                                                <td>{{ $setting?->email }}</td>
                                                <td>
                                                    @if (!empty($setting?->logo_path))
                                                        <img src="{{ asset($setting->logo_path) }}" alt="Logo"
                                                            class="logo" width="70">
                                                    @endif
                                                </td>

                                                <td>{{ $setting?->phone }}</td>
                                                @if (!empty($setting))
                                                    <td>
                                                        <a href="#" class="text-warning me-2" data-toggle="modal"
                                                            data-target="#editGeneralSettingsModal" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @else
                                                    <td>

                                                    </td>
                                                @endif

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Edit General Settings Modal -->
        <div class="modal fade" id="editGeneralSettingsModal" tabindex="-1" role="dialog"
            aria-labelledby="editGeneralSettingsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGeneralSettingsModalLabel">{{ __('dashboard.edit_setting') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateGeneralSettingsForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $setting?->id }}">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.hrm_name') }}</label>
                                    <input type="text" class="form-control" value="{{ $setting?->hrm_name }}"
                                        name="hrm_name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.email') }}</label>
                                    <input type="email" class="form-control" value="{{ $setting?->email }}"
                                        name="email">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.phone') }}</label>
                                    <input type="text" class="form-control" value="{{ $setting?->phone }}"
                                        name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.logo') }}</label>
                                    <input type="file" class="form-control" name="logo_path">
                                </div>
                                @php
                                    $setting = \App\Models\GeneralSetting::first();
                                @endphp

                                <div class="form-group col-md-12 mt-3">
                                    <label for="dashboard_background">{{ __('dashboard.dashboard_background') }}</label>
                                    <input type="file" name="dashboard_background" id="dashboard_background"
                                        accept="image/*" class="form-control">

                                    @if ($setting?->dashboard_background)
                                        <img src="{{ asset($setting->dashboard_background) }}" alt="Background Preview"
                                            style="max-width:300px; margin-top:10px;">
                                    @endif
                                </div>

                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#updateGeneralSettingsForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('settings.update') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.btn-primary')
                            .attr('disabled', true)
                            .text('Updating...');
                    },
                    success: function(response) {
                        $('.btn-primary')
                            .attr('disabled', false)
                            .text('Save Changes');

                        if (response.success) {
                            //  Show success popup
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });

                            //  Reload after popup closes
                            setTimeout(() => {
                                $('#editGeneralSettingsModal').modal('hide');
                                location.reload();
                            }, 1600);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.btn-primary')
                            .attr('disabled', false)
                            .text('Save Changes');

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message ||
                                'Error updating settings.'
                        });
                    }
                });
            });
        });
    </script>

@endsection
