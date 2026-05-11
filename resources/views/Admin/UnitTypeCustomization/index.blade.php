@extends('layouts.app')

@section('title', 'Unit Type Customization')

@section('content')
    <div class="bg-white p-3" style="border-radius:10px;">
        <div class="page-title">{{ __('dashboard.units') }}</div>
        <div class="page-subtitle">{{ __('dashboard.type_customization') }}</div>
        <div class="n-table__top-btns">
            <div>
                @can('type.add')
                    <a href="{{ route('setup-sidebar.typeCustomization.create') }}" class="btn btn-primary mb-2 float-end"
                        style="text-decoration:none;" tabindex="0">
                        {{ __('dashboard.new_unit_type') }}
                    </a>
                @endcan

            </div>
        </div>
        <!-- Users Grid -->
        <div class="k-grid">

            <div class="k-grid-content">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.unit_type') }}</th>
                            <th>{{ __('dashboard.description') }}</th>
                            <th>Website</th>
                            <th>{{ __('dashboard.images_count') }}</th>
                            <th>{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($typeCustomizations as $type)
                            <tr>
                                <td>{{ $type->name ?? '-' }}</td>
                                <td>{{ $type->description }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge {{ $type->is_published_online ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $type->is_published_online ? 'Published' : 'Hidden' }}
                                        </span>
                                        <small class="text-muted">{{ $type->website_slug ?: 'slug-auto' }}</small>
                                    </div>
                                </td>

                                <td> <span class="badge bg-info">
                                        {{ $type->images_count }} {{ __('dashboard.images') }}
                                    </span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        @can('type.edit')
                                            <!-- Edit Button -->
                                            <a href="{{ route('setup-sidebar.typeCustomization.edit', $type->id) }}"
                                                class="btn btn-sm btn-primary" title="{{ __('dashboard.edit') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary " type="button"
                                                id="actionMenu{{ $type->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actionMenu{{ $type->id }}">
                                                <li>
                                                    @can('type.delete')
                                                        <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#deactivateUserModal{{ $type->id }}">
                                                            <i class="bi bi-trash-fill text-danger">
                                                                {{ __('dashboard.delete') }}</i>
                                                        </a>
                                                    @endcan

                                                </li>

                                            </ul>
                                        </div>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('dashboard.no_records_available') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $typeCustomizations->links() }}
        </div>
    </div>
    @foreach ($typeCustomizations as $type)
        <!-- Deactivate Modal -->
        <div class="modal fade" id="deactivateUserModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_unit') }} : {{ $type->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.delete_unit_confirmation') }}</p>
                        <hr>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.typeCustomization.delete', $type->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">
                                {{ __('dashboard.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
