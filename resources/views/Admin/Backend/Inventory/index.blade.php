@extends('layout.master')
@section('title', 'Dashboard | Inventory')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.inventory') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.inventory') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#inventoryModal">
                                    {{ __('dashboard.add_stock') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>

                                                <th>{{ __('dashboard.classify_name') }}</th>
                                                <th>{{ __('dashboard.category_name') }}</th>
                                                <th>{{ __('dashboard.section_name') }}</th>
                                                <th>{{ __('dashboard.warehouse_name') }}</th>
                                                <th>{{ __('dashboard.quantity') }}</th>
                                                <th>{{ __('dashboard.unit') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($inventories as $inventory)
                                                <tr>
                                                    <td>{{ $inventory->product->name }}</td>

                                                    <td>{{ $inventory->product->category->name }}</td>
                                                    <td>{{ $inventory->room->name ?? '-' }}</td>
                                                    <td>{{ $inventory->warehouse->name ?? '-' }}</td>
                                                    <td>{{ $inventory->quantity }}</td>
                                                    <td>{{ $inventory->product->unit ?? '-' }}</td>
                                                    <td>
                                                        <a href="#" class="text-secondary editProductBtn"
                                                            data-toggle="modal"
                                                            data-target="#editInventoryModal{{ __($inventory->id) }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger deleteProductBtn"
                                                            data-toggle="modal"
                                                            data-target="#deleteInventoryModal{{ __($inventory->id) }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>


                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Inventory Modal -->
        <div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addInventoryModalLabel">{{ __('dashboard.add_inventory') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="addInventoryForm" method="POST" action="{{ route('dashboard.inventories.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Warehouse -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.warehouse') }}</label>
                                    <select name="warehouse_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_warehouse') }} --</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Section (optional) -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.section') }}
                                        ({{ __('dashboard.optional') }})</label>
                                    <select name="section_id" class="form-control">
                                        <option value="">-- {{ __('dashboard.select_section') }} --</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Product -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.product') }}</label>
                                    <select name="product_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_classify') }} --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.quantity') }}</label>
                                    <input type="number" name="quantity" class="form-control" min="0" value="0"
                                        required>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.add_inventory') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>



        <!-- Edit Inventory Modal -->
        @foreach ($inventories as $inventory)
            <div class="modal fade" id="editInventoryModal{{ $inventory->id }}" tabindex="-1"
                aria-labelledby="editInventoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editInventoryModalLabel">{{ __('dashboard.edit_inventory') }}</h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <form id="editInventoryForm" method="POST"
                                action="{{ route('dashboard.inventories.update') }}">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" value="{{ $inventory->id }}">

                                <div class="row">
                                    <!-- Warehouse -->
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{ __('dashboard.warehouse') }}</label>
                                        <select name="warehouse_id" class="form-control" required>
                                            <option value="">-- {{ __('dashboard.select_warehouse') }} --</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ $inventory->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Section (optional) -->
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{ __('dashboard.section') }}
                                            ({{ __('dashboard.optional') }})</label>
                                        <select name="section_id" class="form-control">
                                            <option value="">-- {{ __('dashboard.select_section') }} --</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    {{ $inventory->room_id == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Product -->
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{ __('dashboard.product') }}</label>
                                        <select name="product_id" class="form-control" required>
                                            <option value="">-- {{ __('dashboard.select_product') }} --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $inventory->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{ __('dashboard.quantity') }}</label>
                                        <input type="number" name="quantity" class="form-control" min="0"
                                            value="{{ $inventory->quantity }}" required>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="text-end mt-3">
                                    <button type="reset"
                                        class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                    <button type="submit"
                                        class="btn btn-primary">{{ __('dashboard.update_inventory') }}</button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($inventories as $inventory)
            <div class="modal fade" id="deleteInventoryModal{{ __($inventory->id) }}" tabindex="-1"
                aria-labelledby="deleteProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">{{ __('dashboard.delete_inventory') }}</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form id="deleteProductForm" method="POST"
                            action="{{ route('dashboard.inventories.delete') }}">
                            @csrf
                            @method('DELETE')

                            <input type="hidden" name="id" id="delete_product_id" value="{{ $inventory->id }}">

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong id="delete_product_name"></strong>
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach

    </div>

@endsection
