@php
    $passwordModalId = $passwordModalId ?? 'passwordUpdateModal';
    $passwordModalUser = auth()->user();
    $passwordErrorBag = $errors->getBag('updatePassword');
    $requiresVerifiedEmail = $passwordModalUser
        && method_exists($passwordModalUser, 'isSuperAdmin')
        && $passwordModalUser->isSuperAdmin();
    $hasVerifiedEmail = ! $requiresVerifiedEmail
        || ($passwordModalUser && method_exists($passwordModalUser, 'hasVerifiedEmail') && $passwordModalUser->hasVerifiedEmail());
    $shouldOpenPasswordModal = session('status') === 'verification-link-sent'
        || $passwordErrorBag->any();
@endphp

<div class="modal fade" id="{{ $passwordModalId }}" tabindex="-1" aria-labelledby="{{ $passwordModalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $passwordModalId }}Label">{{ __('dashboard.update_password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if ($passwordModalUser)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-semibold">{{ $passwordModalUser->name }}</div>
                            <div class="text-muted small">{{ $passwordModalUser->email }}</div>
                        </div>

                        @if ($requiresVerifiedEmail)
                            <span class="badge {{ $hasVerifiedEmail ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $hasVerifiedEmail ? __('dashboard.email_verified') : __('dashboard.email_not_verified') }}
                            </span>
                        @endif
                    </div>
                @endif

                @if (session('status') === 'password-updated')
                    <div class="alert alert-success">
                        {{ __('dashboard.password_updated_successfully') }}
                    </div>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success">
                        {{ __('dashboard.verification_link_sent') }}
                    </div>
                @endif

                @if ($passwordErrorBag->has('verification'))
                    <div class="alert alert-warning">
                        {{ $passwordErrorBag->first('verification') }}
                    </div>
                @endif

                @if ($requiresVerifiedEmail && ! $hasVerifiedEmail)
                    <div class="alert alert-warning mb-3">
                        {{ __('dashboard.verify_email_before_updating_password') }}
                    </div>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <button type="submit" class="btn btn-primary w-100">
                            {{ __('dashboard.resend_verification_email') }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label" for="{{ $passwordModalId }}CurrentPassword">{{ __('dashboard.current_password') }}</label>
                            <input
                                id="{{ $passwordModalId }}CurrentPassword"
                                type="password"
                                name="current_password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                autocomplete="current-password"
                            >
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="{{ $passwordModalId }}Password">{{ __('dashboard.new_password') }}</label>
                            <input
                                id="{{ $passwordModalId }}Password"
                                type="password"
                                name="password"
                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                autocomplete="new-password"
                            >
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="{{ $passwordModalId }}PasswordConfirmation">{{ __('dashboard.confirm_password') }}</label>
                            <input
                                id="{{ $passwordModalId }}PasswordConfirmation"
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                            >
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            {{ __('dashboard.update_password') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($shouldOpenPasswordModal)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('{{ $passwordModalId }}');

            if (!modalElement || typeof bootstrap === 'undefined') {
                return;
            }

            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        });
    </script>
@endif
