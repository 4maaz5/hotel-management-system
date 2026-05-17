@extends('layout.master')
@section('title', 'Dashboard | platform')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_platforms') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.platforms') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addPlatformModal">
                                    {{ __('dashboard.add_platform') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.platform_name') }}</th>
                                                <th>{{ __('dashboard.contact_person') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.created_at') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($platforms as $platform)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Company -->
                                                    <td>
                                                        {{ $platform->company?->name ?? '-' }}
                                                    </td>

                                                    <!-- Platform Name -->
                                                    <td>
                                                        {{ $platform->name }}
                                                    </td>

                                                    <!-- Contact Person -->
                                                    <td>
                                                        {{ $platform->contact_person ?? '-' }}
                                                    </td>

                                                    <!-- Email -->
                                                    <td>
                                                        {{ $platform->email ?? '-' }}
                                                    </td>

                                                    <!-- Phone -->
                                                    <td>
                                                        {{ $platform->phone ?? '-' }}
                                                    </td>

                                                    <!-- Created Date -->
                                                    <td>
                                                        {{ $platform->created_at?->format('Y-m-d') }}
                                                    </td>

                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editPlatformModal{{ $platform->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deletePlatformModal_{{ $platform->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">
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

        <!-- Add Third Party Platform Modal -->
        <div class="modal fade" id="addPlatformModal" tabindex="-1" role="dialog" aria-labelledby="addPlatformModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header  text-dark">
                        <h5 class="modal-title" id="addPlatformModalLabel">
                            {{ __('dashboard.add_third_party_platform') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form action="{{ route('third-party-platforms.store') }}" method="POST">
                        @csrf

                        <div class="modal-body">
                            <div class="row">

                                <!-- Company -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            {{ __('dashboard.company_name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="company_id" class="form-control">
                                            <option value="">
                                                {{ __('dashboard.select_company') }}
                                            </option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('company_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Platform Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            {{ __('dashboard.platform_name') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Booking.com, Expedia">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.contact_person') }}</label>
                                        <input type="text" name="contact_person" class="form-control"
                                            placeholder="{{ __('dashboard.contact_person') }}">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="contact@example.com">
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                            placeholder="+966XXXXXXXXX">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.address') }}</label>
                                        <textarea name="address" class="form-control" rows="3" placeholder="{{ __('dashboard.address') }}"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Modal Footer -->
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

        @foreach ($platforms as $platform)
            <!-- Edit Platform Modal for Platform ID {{ $platform->id }} -->
            <div class="modal fade" id="editPlatformModal{{ $platform->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editPlatformModalLabel{{ $platform->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header  text-dark">
                            <h5 class="modal-title" id="editPlatformModalLabel{{ $platform->id }}">
                                {{ __('dashboard.edit_third_party_platform') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form action="{{ route('third-party-platforms.update', $platform->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="row">

                                    <!-- Company -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.company_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="company_id" class="form-control" required>
                                                <option value="">{{ __('dashboard.select_company') }}</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}"
                                                        {{ $platform->company_id == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <!-- Platform Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.platform_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $platform->name }}" required>

                                        </div>
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.contact_person') }}</label>
                                            <input type="text" name="contact_person" class="form-control"
                                                value="{{ $platform->contact_person }}">
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.email') }}</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $platform->email }}">
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.phone') }}</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ $platform->phone }}">
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.address') }}</label>
                                            <textarea name="address" class="form-control" rows="3">{{ $platform->address }}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Modal Footer -->
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

        @foreach ($platforms as $platform)
            <div class="modal fade" id="deletePlatformModal_{{ $platform->id }}" tabindex="-1"
                aria-labelledby="deletePlatformModalLabel_{{ $platform->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deletePlatformModalLabel_{{ $platform->id }}">
                                {{ __('dashboard.delete_third_party_platform') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('third-party-platforms.destroy', $platform->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $platform->name }}</strong>?
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
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addPlatformModal'));
                myModal.show();
            });
        </script>
    @endif
@endsection
