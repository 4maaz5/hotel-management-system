@extends('layouts.app')

@section('title', 'View Role')

<style>
    /* Page Header */
    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Form Elements */
    .form__star {
        color: #dc3545;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    /* Multi-language Input */
    .multi-lang-tb,
    .multi-lang-ta {
        position: relative;
    }

    .multi-lang-tb .form-control {
        padding-right: 5rem;
    }

    .multi-lang-ta textarea {
        min-height: 120px;
        resize: vertical;
    }

    .button--primary {
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        background: #4a90e2;
        color: white;
        border: none;
        padding: 0 1rem;
        border-radius: 0 0.375rem 0.375rem 0;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .en_button {
        font-size: 0.875rem;
    }

    .arabic-control {
        direction: rtl;
        text-align: right;
    }

    .u-d-none {
        display: none;
    }

    .u-d-flex {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Form Controls */
    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        width: 100%;
        color: #495057;
    }

    .form-control:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        outline: none;
    }

    .max-height-250 {
        max-height: 250px;
    }

    /* Position Relative */
    .u-pos-relative {
        position: relative;
    }

    /* Radio Collapse */
    .radio-collapse {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .u-mb-24 {
        margin-bottom: 1.5rem;
    }

    .radio-collapse__header {
        display: flex;
        align-items: flex-start;
        padding: 1.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .radio-collapse__header:hover {
        background-color: #f8f9fa;
    }

    .radio-collapse__radio {
        margin-right: 1rem;
        margin-top: 0.25rem;
    }

    .k-radio {
        width: 18px;
        height: 18px;
        border: 2px solid #6c757d;
        border-radius: 50%;
        display: inline-block;
        position: relative;
        cursor: pointer;
    }

    .k-radio:checked {
        border-color: #4a90e2;
    }

    .k-radio:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10px;
        height: 10px;
        background-color: #4a90e2;
        border-radius: 50%;
    }

    .radio-collapse__title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
        font-size: 1rem;
    }

    .radio-collapse__subtitle {
        color: #6c757d;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .font {
        font-weight: 500;
        color: #2c3e50;
    }

    /* Radio Collapse Content */
    .radio-collapse__content {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-top: 1px solid #dee2e6;
    }

    .radio-collapse__item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .radio-collapse__item:hover {
        border-color: #4a90e2;
        background-color: #f0f7ff;
    }

    .radio-collapse__item>div:first-child {
        font-weight: 500;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .radio-collapse__show {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4a90e2;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .radio-collapse__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }

    .radio-collapse__empty img {
        width: 150px;
        height: 150px;
        margin-bottom: 1rem;
        opacity: 0.7;
    }

    /* Form Actions */
    .u-flex-end {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .n-button {
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .n-button--primary {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }

    .n-button--primary:hover {
        background-color: #3a80d2;
        border-color: #3a80d2;
    }

    .n-button--danger-border {
        background-color: transparent;
        color: #dc3545;
        border-color: #dc3545;
    }

    .n-button--danger-border:hover {
        background-color: #dc3545;
        color: white;
    }

    .u-m-end-24 {
        margin-right: 1.5rem;
    }

    /* Responsive Grid */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .col-md-6,
    .col-md-8,
    .col-6 {
        padding: 0 0.75rem;
        margin-bottom: 1.5rem;
    }

    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    .col-md-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }

    .col-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    @media (max-width: 768px) {

        .col-md-6,
        .col-md-8,
        .col-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .radio-collapse__content {
            flex-direction: column;
        }
    }
</style>

@section('content')
   <div class="bg-white p-3" style="border-radius:10px;">
        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.users') }} - {{ __('dashboard.roles') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.view_role') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_role_for_the_system') }}</div>
            </div>
        </div>

        <form method="POST" action="#" novalidate id="form">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">

                    <div class="mb-3">
                        <label>{{ __('dashboard.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="statusToggle" name="status" value="ACTIVE"
                                {{ old('status', $role->status) === 'ACTIVE' ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="statusToggle">
                                {{ old('status', $role->status) === 'ACTIVE' ? __('dashboard.active') : __('dashboard.inactive') }}
                            </label>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Role Name --}}
                    <div class="mb-3">
                        <label class="u-d-flex">
                            {{ __('dashboard.name') }} <span class="form__star">*</span>
                        </label>

                        <input type="text" name="name" class="form-control"
                            placeholder="{{ __('dashboard.enter_role_name') }}" disabled
                            value="{{ old('name', $role->name) }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label>{{ __('dashboard.description') }}</label>
                        <textarea name="description" class="form-control max-height-250" disabled rows="4"
                            placeholder="{{ __('dashboard.enter_role_description') }}">{{ old('description', $role->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="radio-collapse u-mb-24">
                <label class="radio-collapse__header">
                    @php
                        $hasFullAccess = count($rolePermissions) === \Spatie\Permission\Models\Permission::count();
                    @endphp

                    <div class="radio-collapse__radio">
                        <input type="radio" name="access_type" id="full-access" value="full" class="k-radio"
                            {{ $hasFullAccess ? 'checked' : '' }} disabled>
                    </div>
                    <div>
                        <div class="radio-collapse__title">{{ __('dashboard.full_access') }}</div>
                        <div class="radio-collapse__subtitle">
                            {{ __('dashboard.giving_all_previleges') }}
                        </div>
                    </div>
                </label>
            </div>

            <div class="radio-collapse u-mb-24">
                <label class="radio-collapse__header">

                    <div class="radio-collapse__radio">
                        <input type="radio" name="access_type" id="limit-access" value="limited" class="k-radio"
                            {{ !$hasFullAccess ? 'checked' : '' }} disabled>
                    </div>
                    <div>
                        <div class="radio-collapse__title">{{ __('dashboard.limit_access') }}</div>
                        <div class="radio-collapse__subtitle">
                            {{ __('dashboard.select_specific_system_previleges') }}
                        </div>
                    </div>
                </label>

                <div class="radio-collapse__content d-none" id="permission-section">

                    <div class="row">

                        <div class="col-md-6">
                            @foreach ($permissions as $module => $modulePermissions)
                                <div class="radio-collapse__item permission-module" data-module="{{ $module }}">
                                    <div>
                                        {{ ucfirst(str_replace('_', ' ', $module)) }}
                                    </div>
                                    <div class="radio-collapse__show">{{ __('dashboard.show') }} →</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">

                            {{-- Empty State --}}
                            <div id="permission-empty">
                                <div class="radio-collapse__empty text-center">
                                    <div style="font-size:48px">⚙️</div>
                                    <div>{{ __('dashboard.select_a_group_to_manage_permissions') }}</div>
                                </div>
                            </div>

                            {{-- Permission Panels --}}
                            @foreach ($permissions as $module => $modulePermissions)
                                <div class="permission-panel d-none" id="module-{{ $module }}">
                                    <h5 class="mb-3">{{ ucfirst(str_replace('_', ' ', $module)) }}
                                        {{ __('dashboard.permissions') }}</h5>

                                    <div class="row">
                                        @foreach ($modulePermissions as $permission)
                                            @php
                                                $action = explode('.', $permission->name)[1];
                                            @endphp
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}" id="{{ $permission->name }}"
                                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }} disabled>

                                                    <label class="form-check-label" for="{{ $permission->name }}">
                                                        {{ ucfirst($action) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="u-flex-end mt-4">
                <a href="{{ route('setup-sidebar.property-role.index') }}">
                    <button type="button" class="n-button n-button--danger-border u-m-end-24">
                        {{ __('dashboard.back') }}
                    </button>
                </a>
            </div>

        </form>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const fullAccess = document.getElementById('full-access');
            const limitAccess = document.getElementById('limit-access');
            const permissionSection = document.getElementById('permission-section');
            const modules = document.querySelectorAll('.permission-module');
            const panels = document.querySelectorAll('.permission-panel');
            const emptyState = document.getElementById('permission-empty');

            // INITIAL STATE
            if (limitAccess.checked) {
                permissionSection.classList.remove('d-none');
            }

            fullAccess.addEventListener('change', () => {
                permissionSection.classList.add('d-none');
            });

            limitAccess.addEventListener('change', () => {
                permissionSection.classList.remove('d-none');
            });

            modules.forEach(module => {
                module.addEventListener('click', function() {
                    const key = this.dataset.module;

                    panels.forEach(p => p.classList.add('d-none'));
                    emptyState.classList.add('d-none');

                    const target = document.getElementById('module-' + key);
                    if (target) target.classList.remove('d-none');
                });
            });
        });
    </script>
@endpush
