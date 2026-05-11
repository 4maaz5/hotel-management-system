@extends('layout.master')
@section('title', 'Dashboard | Classifies')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_classified') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.classify') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#productModal">
                                    {{ __('dashboard.add_classify') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>

                                                <th>{{ __('dashboard.classify_name') }}</th>
                                                <th>{{ __('dashboard.section_name') }}</th>
                                                <th>{{ __('dashboard.sku') }}</th>
                                                <th>{{ __('dashboard.unit') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>

                                                    <td>{{ $product->category->name }}</td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ $product->unit }}</td>
                                                    <td>

                                                        <a href="#" class="text-secondary editProductBtn"
                                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                            data-category-id="{{ $product->category_id }}"
                                                            data-sku="{{ $product->sku }}"
                                                            data-unit="{{ $product->unit }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger deleteProductBtn"
                                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                            data-toggle="modal" data-target="#deleteProductModal">
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

        <!-- Add Product Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">{{ __('dashboard.add_classify') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form id="addProductForm" method="POST" action="{{ route('dashboard.products.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Product Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.classify_name') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="" required>
                                </div>

                                <!-- Category -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.category') }}</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_category') }} --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- SKU -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.sku') }}
                                        ({{ __('dashboard.optional') }})</label>
                                    <input type="text" class="form-control" name="sku" placeholder="e.g., PROD123">
                                </div>

                                <!-- Unit -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.unit') }}</label>
                                    <input type="text" class="form-control" name="unit" value="pcs"
                                        placeholder="e.g., pcs, kg" required>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.add_classify') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">{{ __('dashboard.edit_product') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editProductForm" method="POST" action="{{ route('dashboard.products.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" id="edit_product_id">

                            <div class="row">
                                <!-- Product Name -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.product_name') }}</label>
                                    <input type="text" id="edit_name" name="name" class="form-control" required>
                                </div>

                                <!-- Category -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.category') }}</label>
                                    <select name="category_id" id="edit_category_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_category') }} --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- SKU -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.sku') }} ({{ __('dashboard.optional') }})</label>
                                    <input type="text" id="edit_sku" name="sku" class="form-control"
                                        placeholder="e.g., PROD123">
                                </div>

                                <!-- Unit -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.unit') }}</label>
                                    <input type="text" id="edit_unit" name="unit" class="form-control"
                                        value="pcs" placeholder="e.g., pcs, kg" required>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_product') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>



        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_classify') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="deleteProductForm" method="POST" action="{{ route('dashboard.products.delete') }}">
                        @csrf
                        @method('DELETE')

                        <input type="hidden" name="id" id="delete_product_id">

                        <div class="modal-body text-center">
                            <p>{{ __('dashboard.classify_delete') }}
                                <strong id="delete_product_name"></strong>?
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


    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('submit', '#addProductForm', function(e) {
            e.preventDefault();

            let $form = $(this);
            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#productModal').modal('hide');
                        $form[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success / نجاح',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                        // Append new product to table
                        let newRow = `
<tr>
    <td>New</td>
    <td>${res.data.name}</td>
    <td>${res.data.category}</td>
    <td>${res.data.warehouse}</td>
    <td>${res.data.room ?? '-'}</td>
    <td>${res.data.stock}</td>
    <td>${res.data.price ?? '-'}</td>
    <td>
        <a href="#" class="text-secondary editProductBtn"
           data-id="${res.data.id}"
           data-name="${res.data.name}"
           data-category-id="${res.data.category_id}"
           data-warehouse-id="${res.data.warehouse_id}"
           data-room-id="${res.data.room_id}"
           data-stock="${res.data.stock}"
           data-price="${res.data.price}">
           <i class="fas fa-edit"></i>
        </a>

        <a href="#" class="text-danger deleteProductBtn"
           data-id="${res.data.id}"
           data-name="${res.data.name}">
           <i class="fas fa-trash-alt"></i>
        </a>
    </td>
</tr>`;

                        $('#productsTable tbody').append(newRow);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error / خطأ',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.editProductBtn', function() {
            $('#edit_product_id').val($(this).data('id'));
            $('#edit_name').val($(this).data('name'));
            $('#edit_category_id').val($(this).data('category-id'));

            $('#edit_sku').val($(this).data('sku'));
            $('#edit_unit').val($(this).data('unit'));

            $('#editProductModal').modal('show');
        });

        $(document).on('submit', '#editProductForm', function(e) {
            e.preventDefault();

            let $form = $(this);
            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#editProductModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update row (simplest: reload page)
                        location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.deleteProductBtn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');

            $('#delete_product_id').val(id);
            $('#delete_product_name').text(name);

            $('#deleteProductModal').modal('show');
        });

        $(document).on('submit', '#deleteProductForm', function(e) {
            e.preventDefault();

            let $form = $(this);
            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',

                success: function(res) {
                    if (res.success) {
                        $('#deleteProductModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                        // Remove row from table
                        $("#row_" + res.id).remove();
                    }
                },

                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Unable to delete product'
                    });
                },

                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
