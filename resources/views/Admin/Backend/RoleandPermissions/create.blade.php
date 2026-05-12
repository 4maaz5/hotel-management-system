@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.add_new_role') }}</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('dashboard.setting.role.store') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label>{{ __('dashboard.role_name') }}</label>
                                        <input type="text" name="role" class="form-control" placeholder="">
                                        @error('role')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <h5 class="mb-3">{{ __('dashboard.assign_permissions') }}</h5>

                                    @php
                                        // Optional: Group permissions by module
                                        $groupedPermissions = $permissions->groupBy(function ($perm) {
                                            $name = $perm->name;
                                            if (str_contains($name, '.')) {
                                                return ucfirst(explode('.', $name)[0]);
                                            }
                                            $parts = explode('_', $name);
                                            return ucfirst($parts[1] ?? 'Other');
                                        });
                                    @endphp

                                    @foreach ($groupedPermissions as $group => $perms)
                                        <div class="mb-3">
                                            {{-- <label class="fw-bold d-block">{{ $group }}</label> --}}
                                            <label class="fw-bold d-block">
                                                {{ __('permission_groups.' . $group) }}
                                            </label>


                                            <div class="row">
                                                @foreach ($perms as $permission)
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->name }}"
                                                                id="{{ $permission->name }}">
                                                            {{-- <label class="form-check-label"
                                                                for="{{ $permission->name }}">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</label> --}}
                                                            <label class="form-check-label" for="{{ $permission->name }}">
                                                                {{ __('permissions.' . $permission->name) }}
                                                            </label>
                                                            @error('permissions')
                                                                <small class="text-danger d-block">{{ $message }}</small>
                                                            @enderror

                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-end">
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('dashboard.save_role_permissions') }}</button>
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
