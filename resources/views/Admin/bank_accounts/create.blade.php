@extends('layouts.app')

@section('title', 'Add Bank account')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category ">{{ __('dashboard.financials_bank_list') }}</div>
        <div class="page-header">
            <div>
                <a href="{{ route('setup-sidebar.bank_account.index') }}" type="submit" class="btn btn-secondary float-end">
                    {{ __('dashboard.back') }}
                </a>
                <h2 class="page-header__title">{{ __('dashboard.new_bank') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.fill_the_information_to_add_new_bank') }}
                </div>
            </div>

        </div>

        <div class="container-fluid">

            <form action="{{ route('setup-sidebar.bank_account.store') }}" method="POST">
                @csrf

                {{-- Bank Name --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.bank_name') }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Account Number --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.account_number') }} *</label>
                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}"
                        required>
                    @error('account_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Currency --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.currency') }} *</label>
                    <select name="currency" class="form-control" required>
                        <option value="SAR" selected>SAR</option>
                    </select>
                    @error('currency')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- IBAN --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.iban') }}</label>
                    <input type="text" name="iban" class="form-control" value="{{ old('iban') }}">
                    @error('iban')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('dashboard.description') }}</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>


                <button type="submit" class="btn btn-primary">
                    {{ __('dashboard.create_bank') }}
                </button>

            </form>
        </div>

    </main>
@endsection
