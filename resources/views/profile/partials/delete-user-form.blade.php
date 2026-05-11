<form method="post" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')

    <p class="text-danger mb-3">
        {{ __('dashboard.delete_account_warning') }}
    </p>

    <div class="mb-3">
        <label class="form-label">{{ __('dashboard.confirm_password') }}</label>
        <input type="password" name="password" class="form-control">
    </div>

    <button class="btn btn-danger">
        {{ __('dashboard.delete_account') }}
    </button>
</form>
