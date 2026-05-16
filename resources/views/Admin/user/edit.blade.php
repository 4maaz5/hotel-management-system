@extends('layouts.app')

@section('content')
<div class="bg-white p-3" style="border-radius:10px;">
    <div class="page-title">{{ __('dashboard.users') }}</div>
    <div class="page-subtitle">{{ __('dashboard.edit_user') }}</div>

    <form action="{{ route('setup-sidebar.property-user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @php
            $profile = $user->profile_data ?? [];
            $employment = $user->employment_data ?? [];
            $contact = $user->contact_info ?? [];
            $selectedPropertyIds = collect(old('property_ids', $user->assignedProperties->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
            $defaultPropertyId = old('default_property_id', $user->property?->id);
        @endphp

        <!-- User Type & Role -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.user_type') }}<span class="text-danger">*</span></label>
                <select class="form-select" name="user_type" required>
                    <option value="">{{ __('dashboard.select_user_type') }}</option>
                    <option value="admin" {{ $user->user_type == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ $user->user_type == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ $user->user_type == 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="worker" {{ $user->user_type == 'worker' ? 'selected' : '' }}>Worker</option>
                    <option value="receptionist" {{ $user->user_type == 'receptionist' ? 'selected' : '' }}>Receptionist
                    </option>
                </select>
                @error('user_type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.role') }}<span class="text-danger">*</span></label>
                <select class="form-select" name="user_role" required>
                    <option value="">{{ __('dashboard.select_role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ $user->roles->pluck('id')->contains($role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.status') }}</label>
                <select class="form-select" name="status" disabled>
                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <!-- User Data -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.user_data') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.username') }}<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username"
                    value="{{ $profile['username'] ?? $user->username }}" required>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.password') }}</label>
                <input type="password" class="form-control" name="password" placeholder="Leave empty to keep current">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.confirm_password') }}</label>
                <input type="password" class="form-control" name="password_confirmation"
                    placeholder="Leave empty to keep current">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.default_language') }}</label>
                <select class="form-select" name="default_lang">
                    <option value="en" {{ $user->default_language == 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ $user->default_language == 'ar' ? 'selected' : '' }}>Arabic</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                <input type="date" class="form-control" name="expiry_date" value="{{ $user->expiry_date ?? '' }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.full_name') }}</label>
                <input type="text" class="form-control" name="full_name" value="{{ $profile['first_name_en'] ?? '' }}"
                    required>
            </div>
        </div>

        <!-- Employment Data -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.employment_data') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.title') }}</label>
                <select class="form-select" name="title">
                    <option value="">Select Title</option>
                    @foreach (['mr' => 'Mr.', 'mrs' => 'Mrs.', 'ms' => 'Ms.', 'dr' => 'Dr.'] as $key => $val)
                        <option value="{{ $key }}" {{ ($employment['title'] ?? '') == $key ? 'selected' : '' }}>
                            {{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.gender') }}</label>
                <select class="form-select" name="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ ($employment['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ ($employment['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female
                    </option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.date_of_birth') }}</label>
                <input type="date" class="form-control" name="date_of_birth"
                    value="{{ $employment['date_of_birth'] ?? '' }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.code') }}</label>
                <input type="text" class="form-control" name="employee_code"
                    value="{{ $employment['employee_code'] ?? '' }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.nationality') }}</label>
                <input type="text" class="form-control" name="nationality"
                    value="{{ $employment['nationality'] ?? '' }}">
            </div>
           <div class="col-md-4 mb-3">
    <label class="form-label">{{ __('dashboard.department') }}</label>

    <select class="form-select" name="department">
        <option value="">{{ __('dashboard.select_department') }}</option>

        <option value="hr"
            {{ old('department', $employment['department'] ?? '') == 'hr' ? 'selected' : '' }}>
            {{ __('dashboard.hr') }}
        </option>

        <option value="it"
            {{ old('department', $employment['department'] ?? '') == 'it' ? 'selected' : '' }}>
            {{ __('dashboard.it') }}
        </option>

        <option value="finance"
            {{ old('department', $employment['department'] ?? '') == 'finance' ? 'selected' : '' }}>
            {{ __('dashboard.finance') }}
        </option>

        <option value="operations"
            {{ old('department', $employment['department'] ?? '') == 'operations' ? 'selected' : '' }}>
            {{ __('dashboard.operations') }}
        </option>
    </select>

    @error('department')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.position') }}</label>
                <input type="text" class="form-control" name="position" value="{{ $employment['position'] ?? '' }}">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">{{ __('dashboard.description') }}</label>
                <textarea class="form-control" rows="3" name="description">{{ $employment['description_en'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Contact Info -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.contact_information') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.mobile_number') }}</label>
                <input type="text" class="form-control" name="employee_contact"
                    value="{{ $contact['mobile_number'] ?? '' }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.email') }}</label>
                <input type="email" class="form-control" name="employee_email" value="{{ $contact['email'] ?? '' }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.business_mobile_no') }}</label>
                <input type="text" class="form-control" name="business_mobile_no"
                    value="{{ $contact['business_phone'] ?? '' }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">{{ __('dashboard.address') }}</label>
                <textarea class="form-control" rows="2" name="address">{{ $contact['address'] ?? '' }}</textarea>
            </div>
        </div>

        <!-- Assignment -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.assignment') }}</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.default_branch') }}</label>
                <select class="form-select" name="default_property_id">
                    <option value="">{{ __('dashboard.select_default_branch') }}</option>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}" @selected((int) $defaultPropertyId === (int) $property->id)>
                            {{ $property->property_name_en }}
                        </option>
                    @endforeach
                </select>
                @error('default_property_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.allowed_branches') }}</label>
                <select class="form-select" name="property_ids[]" multiple size="{{ max(4, min(8, count($properties))) }}">
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}" @selected(in_array((int) $property->id, $selectedPropertyIds, true))>
                            {{ $property->property_name_en }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __('dashboard.branch_assignment_help') }}</small>
                @error('property_ids')
                    <span class="text-danger d-block">{{ $message }}</span>
                @enderror
                @error('property_ids.*')
                    <span class="text-danger d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Photo / Signature -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.photo') }}</label>
                <input type="file" class="form-control" name="photo" accept="image/*">
                @if (!empty($profile['photo_path']))
                    <img src="{{ asset('storage/' . $profile['photo_path']) }}" class="img-thumbnail mt-2"
                        width="150">
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.signature') }}</label>
                <input type="file" class="form-control" name="signature" accept="image/*">
                @if (!empty($profile['signature_path']))
                    <img src="{{ asset('storage/' . $profile['signature_path']) }}" class="img-thumbnail mt-2"
                        width="150">
                @endif
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('setup-sidebar.property-user.index') }}"
                class="btn btn-secondary">{{ __('dashboard.back') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('dashboard.update_user') }}</button>
        </div>
    </form>
</div>
@endsection
