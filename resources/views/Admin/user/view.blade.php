@extends('layouts.app')

@section('content')
<div class="bg-white p-3" style="border-radius:10px;">
    <div class="page-title">{{ __('dashboard.users') }}</div>
    <div class="page-subtitle">{{ __('dashboard.view_user') }}</div>

    <form>
        @php
            $profile = $user->profile_data ?? [];
            $employment = $user->employment_data ?? [];
            $contact = $user->contact_info ?? [];
        @endphp
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.user_type') }}</label>
                <input type="text" class="form-control" value="{{ ucfirst($user->user_type) }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.role') }}</label>
                <input type="text" class="form-control" value="{{ optional($user->roles->first())->name }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.role_description') }}</label>
                <input type="text" class="form-control" value="-" readonly>
            </div>
        </div>

        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.user_data') }}</h6>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.username') }}</label>
                <input type="text" class="form-control" value="{{ $profile['username'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.default_language') }}</label>
                <input type="text" class="form-control" value="{{ strtoupper($user->default_language) }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                <input type="date" class="form-control" value="{{ $user->expiry_date ?? '' }}" readonly>
            </div>
        </div>

        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.employment_data') }}</h6>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.title') }}</label>
                <input type="text" class="form-control" value="{{ $employment['title'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.full_name') }}</label>
                <input type="text" class="form-control" value="{{ $profile['first_name_en'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.status') }}</label>
                <input type="text" class="form-control" value="{{ ucfirst($user->status) }}" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.gender') }}</label>
                <input type="text" class="form-control" value="{{ ucfirst($employment['gender'] ?? '') }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.date_of_birth') }}</label>
                <input type="date" class="form-control" value="{{ $employment['date_of_birth'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.code') }}</label>
                <input type="text" class="form-control" value="{{ $employment['employee_code'] ?? '' }}" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.nationality') }}</label>
                <input type="text" class="form-control" value="{{ strtoupper($employment['nationality'] ?? '') }}"
                    readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.department') }}</label>
                <input type="text" class="form-control" value="{{ ucfirst($employment['department'] ?? '') }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.position') }}</label>
                <input type="text" class="form-control" value="{{ $employment['position'] ?? '' }}" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">{{ __('dashboard.description') }}</label>
                <textarea class="form-control" rows="3" readonly>{{ $employment['description_en'] ?? '' }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.photo') }}</label><br>
                @if (!empty($profile['photo_path']))
                    <img src="{{ asset('storage/' . $profile['photo_path']) }}" class="img-thumbnail" width="150">
                @else
                    <span class="text-muted">—</span>
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.signature') }}</label><br>
                @if (!empty($profile['signature_path']))
                    <img src="{{ asset('storage/' . $profile['signature_path']) }}" class="img-thumbnail" width="150">
                @else
                    <span class="text-muted">—</span>
                @endif
            </div>
        </div>

        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.contact_information') }}</h6>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.mobile_number') }}</label>
                <input type="text" class="form-control" value="{{ $contact['mobile_number'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.email') }}</label>
                <input type="email" class="form-control" value="{{ $contact['email'] ?? '' }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.business_mobile_no') }}</label>
                <input type="text" class="form-control" value="{{ $contact['business_phone'] ?? '' }}" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">{{ __('dashboard.address') }}</label>
                <textarea class="form-control" rows="2" readonly>{{ $contact['address'] ?? '' }}</textarea>
            </div>
        </div>

        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.assignment') }}</h6>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.default_branch') }}</label>
                <input type="text" class="form-control"
                    value="{{ $user->property?->property_name_en ?? $user->property?->property_name_ar ?? '-' }}"
                    readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.allowed_branches') }}</label>
                <input type="text" class="form-control"
                    value="{{ $user->assignedProperties->pluck('property_name_en')->filter()->implode(', ') ?: ($user->property?->property_name_en ?? '-') }}"
                    readonly>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <a href="{{ route('setup-sidebar.property-user.index') }}" class="btn btn-secondary">
                {{ __('dashboard.back') }}
            </a>
        </div>

    </form>
</div>
@endsection
