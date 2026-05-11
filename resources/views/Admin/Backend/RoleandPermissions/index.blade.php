@extends('layout.master')
@section('title', 'Dashboard | Roles')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.role_and_permissions') }}</h4>
                                <a href="{{ route('dashboard.setting.role.create') }}" type="button"
                                    class="btn btn-primary">{{ __('dashboard.create_role') }}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.role') }}</th>
                                                <th>{{ __('dashboard.permissions') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td>{{ $role->name }}</td>
                                                    <td>
                                                        @if ($role->permissions->isEmpty())
                                                            <span
                                                                class="text-muted">{{ __('dashboard.no_permissions') }}</span>
                                                        @elseif($role->name === 'Super Admin')
                                                            {{ __('dashboard.all_permissions') }}
                                                        @else
                                                            {{-- {{ $role->permissions->pluck('name')->join(', ') }} --}}
                                                            {{ $role->permissions->map(fn($permission) => __('permissions.' . $permission->name))->join('، ') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('dashboard.setting.role.edit', $role->id) }}"
                                                            class="text-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteRoleModal_{{ $role->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @foreach ($roles as $role)
            <!-- Delete Modal -->
            <div class="modal fade" id="deleteRoleModal_{{ $role->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">{{ __('dashboard.delete_role') }}</h5>
                            <button type="button" class="btn-close" data-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            {{ __('dashboard.delete_role_modal') }} <strong>{{ $role->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('dashboard.setting.role.destroy', $role->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                            </form>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    @endsection
