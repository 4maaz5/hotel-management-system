@extends('layouts.app')

@section('content')
@php
    $selectedPropertyIds = collect(old('property_ids', []))->map(fn ($id) => (int) $id)->all();
    $defaultPropertyId = old('default_property_id');
@endphp
<div class="bg-white p-3" style="border-radius:10px;">
    <div class="page-title">{{ __('dashboard.users') }}</div>
    <div class="page-subtitle">{{ __('dashboard.new_user') }}<br>{{ __('dashboard.create_a_new_user') }}</div>

    <form action="{{ route('setup-sidebar.property-user.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- User Type & Role Section -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.user_type') }}<span class="text-danger">*</span></label>
                <select class="form-select" required name="user_type">
                    <option value="">{{ __('dashboard.select_user_type') }}</option>
                    <option value="admin">{{ __('dashboard.admin') }}</option>
                    <option value="manager">{{ __('dashboard.manager') }}</option>
                    <option value="employee">{{ __('dashboard.employee') }}</option>
                    <option value="worker">{{ __('dashboard.worker') }}</option>
                    <option value="receptionist">{{ __('dashboard.receptionist') }}</option>
                </select>
                @error('user_type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.role') }}<span class="text-danger">*</span></label>
                <select class="form-select" name="user_role">
                    <option value="">{{ __('dashboard.select_role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('user_role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.role_description') }}</label>
                <input type="text" class="form-control" readonly placeholder="-" name="role_description">
            </div>
        </div>

        <!-- User Data Section -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.user_data') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.username') }}<span class="text-danger">*</span></label>
                <input type="text" class="form-control" required name="username">
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.password') }}<span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" required>

                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.confirm_password') }}<span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password_confirmation" required>

                @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <small class="text-muted">{{ __('dashboard.make_sure_to_follow_password_policy') }}</small>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.default_language') }}<span class="text-danger">*</span></label>
                <select class="form-select" required name="default_lang">
                    <option value="">{{ __('dashboard.select_language') }}</option>
                    <option value="en">{{ __('dashboard.english') }}</option>
                    <option value="ar">{{ __('dashboard.arabic') }}</option>
                </select>
                @error('default_lang')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                <input type="date" class="form-control" name="expiry_date">
                @error('expiry_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Employment Data Section -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.employment_data') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.title') }}</label>
                <select class="form-select" name="title">
                    <option value="">{{ __('dashboard.select_title') }}</option>
                    <option value="mr">Mr.</option>
                    <option value="mrs">Mrs.</option>
                    <option value="ms">Ms.</option>
                    <option value="dr">Dr.</option>
                </select>
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.full_name') }}<span class="text-danger">*</span> <i
                        class="bi bi-question-circle"></i></label>
                <input type="text" class="form-control" required name="full_name">
                @error('full_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.status') }}</label>
                <select class="form-select" name="status">
                    <option value="">{{ __('dashboard.select_status') }}</option>
                    <option value="active">{{ __('dashboard.active') }}</option>
                    <option value="inactive">{{ __('dashboard.inactive') }}</option>
                </select>
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="col-md-4 mb-3">
                <label class="form-label">العربية</label>
                <input type="text" class="form-control">
            </div> --}}
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.gender') }}</label>
                <select class="form-select" name="gender">
                    <option value="">{{ __('dashboard.select_gender') }}</option>
                    <option value="male">{{ __('dashboard.male') }}</option>
                    <option value="female">{{ __('dashboard.female') }}</option>
                </select>
                @error('gender')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.date_of_birth') }}</label>
                <input type="date" class="form-control" name="date_of_birth">
                @error('date_of_birth')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.code') }}</label>
                <input type="text" class="form-control" name="employee_code">
                @error('employee_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.nationality') }}</label>
                <select class="form-select" name="nationality">
                    <option value="">{{ __('dashboard.select_nationality') }}</option>
                    <option value="sa">Saudi Arabia</option>
                    <option value="ae">United Arab Emirates</option>
                    <option value="us">United States</option>
                    <option value="uk">United Kingdom</option>
                </select>
                @error('nationality')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.department') }}</label>
                <select class="form-select" name="department">
                    <option value="">{{ __('dashboard.select_department') }}</option>
                    <option value="hr">Human Resources</option>
                    <option value="it">IT</option>
                    <option value="finance">Finance</option>
                    <option value="operations">Operations</option>
                </select>
                @error('department')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.position') }}</label>
                <input type="text" class="form-control" name="position">
                @error('position')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.description') }}</label>
                <textarea class="form-control" rows="3" name="description"></textarea>
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="col-md-6 mb-3">
                <label class="form-label">العربية</label>
                <textarea class="form-control" rows="3"></textarea>
            </div> --}}
        </div>

        <!-- Photo and Signature Upload -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.photo') }}</label>
                <div class="border border-2 border-dashed p-4 text-center rounded">
                    <i class="bi bi-upload fs-3"></i>
                    <div class="mt-2">{{ __('dashboard.upload') }}</div>
                    <small class="text-muted">{{ __('dashboard.minimum_size') }} 400px*300px</small>
                    <input type="file" class="form-control mt-2" name="photo" accept="image/*">
                    @error('employee_image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('dashboard.signature') }}</label>
                <div class="border border-2 border-dashed p-4 text-center rounded">
                    <i class="bi bi-upload fs-3"></i>
                    <div class="mt-2">{{ __('dashboard.upload') }}</div>
                    <small class="text-muted">{{ __('dashboard.minimum_size') }} 400px*300px</small>
                    <input type="file" class="form-control mt-2" name="signature" accept="image/*">
                    @error('employee_signature')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <h6 class="mt-4 mb-3 pb-2 border-bottom">{{ __('dashboard.contact_information') }}</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.mobile_number') }}<span class="text-danger">*</span></label>
                <div class="input-group">
                    <select class="form-select" style="max-width: 100px;">
                        <option value="+966">+966</option>
                        <option value="+971">+971</option>
                        <option value="+1">+1</option>
                        <option value="+44">+44</option>
                    </select>
                    <input type="tel" class="form-control" required name="employee_contact">
                    @error('employee_contact')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.email') }}</label>
                <input type="email" class="form-control" name="employee_email">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.business_mobile_no') }}</label>
                <div class="input-group">
                    <select class="form-select" style="max-width: 100px;">
                        <option value="+966">+966</option>
                        <option value="+971">+971</option>
                        <option value="+1">+1</option>
                        <option value="+44">+44</option>
                    </select>
                    <input type="tel" class="form-control" name="business_mobile_no">
                    @error('business_mobile_no')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">{{ __('dashboard.address') }}</label>
                <textarea class="form-control" rows="2" name="address"></textarea>
                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Assignment Section -->
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
        </div>

        <div class="row">
            <div class="col-md-5">
                {{-- <div class="border rounded p-2">
                    <div class="fw-bold mb-2">Available Properties <span class="text-muted">Page : 1</span></div>
                    <select class="form-select" multiple size="8">
                        <option value="prop1">Property 1</option>
                        <option value="prop2">Property 2</option>
                        <option value="prop3">Property 3</option>
                        <option value="prop4">Property 4</option>
                        <option value="prop5">Property 5</option>
                    </select>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">↑</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">⇈</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">↓</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">⇊</button>
                    </div>
                </div> --}}
            </div>

            <div class="col-md-2 d-flex align-items-center justify-content-center">
                {{-- <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-primary btn-sm">→</button>
                    <button type="button" class="btn btn-primary btn-sm">⇒</button>
                    <button type="button" class="btn btn-primary btn-sm">←</button>
                    <button type="button" class="btn btn-primary btn-sm">⇐</button>
                </div> --}}
            </div>

            <div class="col-md-5">
                {{-- <div class="border rounded p-2">
                    <div class="fw-bold mb-2">Selected Properties <span class="text-muted">Page : 1</span></div>
                    <select class="form-select" multiple size="8">
                        <option value="sprop1">Selected Property 1</option>
                    </select>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">↑</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">⇈</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">↓</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">⇊</button>
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
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

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary">{{ __('dashboard.discard') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('dashboard.create_user') }}</button>
        </div>
    </form>
</div>

@endsection
