@extends('layouts.app')

@section('title', 'Edit Bank Acccount')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">
            {{ __('dashboard.financials_bank_list') }}
        </div>

        <div class="page-header">
            <div>
                <a href="{{ route('setup-sidebar.bank_account.index') }}" type="submit" class="btn btn-secondary float-end">
                    {{ __('dashboard.back') }}
                </a>
                <h2 class="page-header__title">
                    {{ __('dashboard.edit_bank') }}
                </h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.update_bank_information') }}
                </div>
            </div>
        </div>

        <div class="container-fluid">

            <form action="{{ route('setup-sidebar.bank_account.update', $bank->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Bank Name --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.bank_name') }} *
                    </label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $bank->name) }}"
                        required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Account Number --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.account_number') }} *
                    </label>
                    <input type="text" name="account_number" class="form-control"
                        value="{{ old('account_number', $bank->account_number) }}" required>
                    @error('account_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Currency --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.currency') }} *
                    </label>
                    <select name="currency" class="form-control" required>
                        <option value="SAR" {{ old('currency', $bank->currency) == 'SAR' ? 'selected' : '' }}>
                            SAR
                        </option>
                    </select>
                </div>

                {{-- IBAN --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.iban') }}
                    </label>
                    <input type="text" name="iban" class="form-control" value="{{ old('iban', $bank->iban) }}">
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">
                        {{ __('dashboard.description') }}
                    </label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $bank->description) }}</textarea>
                </div>

                {{-- Status Toggle --}}
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                        {{ old('is_active', $bank->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ __('dashboard.active') }}
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.update_bank') }}
                </button>

            </form>

        </div>

    </main>


@endsection
