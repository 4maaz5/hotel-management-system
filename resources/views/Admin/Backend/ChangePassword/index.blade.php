@extends('layout.master')
@section('title', 'Dashboard | Password')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.change_password') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user.change-password') }}" method="POST">
                                    @csrf

                                    <!-- Current Password -->
                                    <div class="form-group mb-3">
                                        <label>{{ __('dashboard.current_password') }}</label>
                                        <input type="password" name="current_password" class="form-control"
                                            placeholder="{{ __('dashboard.current_password') }}" required>
                                        @error('current_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="form-group mb-3">
                                        <label>{{ __('dashboard.new_password') }}</label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="{{ __('dashboard.new_password') }}" required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div class="form-group mb-3">
                                        <label>{{ __('dashboard.confirm_password') }}</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="{{ __('dashboard.confirm_password') }}" required>
                                        @error('password_confirmation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Buttons -->
                                    <div class="text-end">
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('dashboard.update_password') }}</button>
                                    </div>
                                </form>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
