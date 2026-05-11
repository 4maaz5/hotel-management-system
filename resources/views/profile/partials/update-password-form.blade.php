<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label class="form-label">{{ __('dashboard.current_password') }}</label>
        <input type="password" name="current_password" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('dashboard.new_password') }}</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('dashboard.confirm_password') }}</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>

    <button class="btn btn-warning">
        {{ __('dashboard.update_password') }}
    </button>
</form>
