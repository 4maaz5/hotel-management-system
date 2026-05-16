@extends('layout.master')
@section('title', 'Dashboard | Expenses')
@section('main')

    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center">{{ __('dashboard.administrative_expenses') }}</h1>

        <div class="cards-grid expenses-grid">
            @forelse($expenseCards as $expense)
                <div class="card-item">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ __('dashboard.item_name') }}:</strong> {{ $expense->item_name }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.branch') }}:</strong>
                                {{ $expense->branch->name ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.amount') }}:</strong>
                                {{ number_format($expense->amount, 2) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.purchase_date') }}:</strong>
                                {{ $expense->expense_date }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.description') }}:</strong>
                                {{ $expense->description ?? '-' }}</p>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    {{-- <p>No expense records found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $expenseCards->links('pagination::bootstrap-5') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.administrative_expenses') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addExpenseModal">
                                    {{ __('dashboard.add_expense') }}
                                </button>

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="expenseFilterForm" class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <select id="filter_branch" name="branch_id" class="form-control">
                                                    <option value="">{{ __('dashboard.all_branches') }}</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <input id="filter_item" type="text" name="item_name" class="form-control"
                                                    placeholder="{{ __('dashboard.Item_Name') }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input id="filter_start" type="date" name="start_date"
                                                    class="form-control" placeholder="{{ __('dashboard.date_from') }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input id="filter_end" type="date" name="end_date" class="form-control"
                                                    placeholder="{{ __('dashboard.date_to') }}">
                                            </div>

                                            <div class="col-md-1">
                                                <button id="filterBtn" type="button"
                                                    class="btn btn-primary w-100">{{ __('dashboard.filter') }}</button>
                                            </div>
                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.Item_Name') }}</th>
                                                <th>{{ __('dashboard.branch_name') }}</th>
                                                <th>{{ __('dashboard.amount') }}</th>
                                                <th>{{ __('dashboard.purchase_date') }}</th>
                                                <th>{{ __('dashboard.description') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="expenseTableBody">
                                            @include('Admin.Backend.partials.expenses_rows', [
                                                'expenses' => $expenses,
                                            ])
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Add Expense Modal -->
        <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_expense') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <form id="addExpenseForm" action="{{ route('dashboard.finance.expense.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div id="itemsContainer">

                                <!-- ITEM BLOCK -->
                                <div class="expense-item border rounded p-3 mb-4 bg-light">

                                    <h6 class="mb-3 fw-bold">{{ __('dashboard.expense_item') }}</h6>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>{{ __('dashboard.invoice_number') }}</label>
                                            <input type="number" name="invoice_number[]" class="form-control">
                                            <span class="text-danger error-text invoice_number_error"></span>
                                        </div>

                                        <div class="form-group col-md-6 mb-3">
                                            <label>{{ __('dashboard.Item_Name') }}</label>
                                            <input type="text" name="name[]" class="form-control">
                                            <span class="text-danger error-text name_error"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>{{ __('dashboard.item_quantity') }}</label>
                                            <input type="number" name="item_quantity[]" class="form-control">
                                            <span class="text-danger error-text item_quantity_error"></span>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="form-label">{{ __('dashboard.branch') }}:</label>
                                            <label
                                                class="form-label">{{ __('dashboard.company') }}→{{ __('dashboard.brand') }}→{{ __('dashboard.branch') }}</label>
                                            <select name="branch_id" class="form-control" required>
                                                <option value="">-- {{ __('dashboard.select_branch') }} --</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">
                                                        {{ $branch->company->name ?? 'N/A' }} →
                                                        {{ $branch->brand->name ?? 'N/A' }} → {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label>{{ __('dashboard.amount') }}</label>
                                            <input type="number" step="0.01" name="amount[]" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6 mb-3">
                                            <label>{{ __('dashboard.purchase_date') }}</label>
                                            <input type="date" name="purchase_date[]" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 mb-3">
                                            <label>{{ __('dashboard.file') }}</label>
                                            <input type="file" name="file[]" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>{{ __('dashboard.description') }}</label>
                                        <textarea name="description[]" rows="3" class="form-control"></textarea>
                                    </div>

                                </div><!-- /expense-item -->
                            </div><!-- /itemsContainer -->

                            <!-- ADD ITEM BUTTON -->
                            <div class="text-start mb-3">
                                <button type="button" id="addMoreItem" class="btn btn-secondary">
                                    + {{ __('dashboard.add_more_item') }}
                                </button>
                            </div>

                            <!-- SAVE BUTTON -->
                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.save_expense') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Expense Modal -->
        <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel">{{ __('dashboard.edit_expense') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editExpenseForm" action="{{ route('dashboard.finance.expense.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id">
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <label>{{ __('dashboard.invoice_number') }}</label>
                                    <input type="number" name="invoice_number" class="form-control">
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label>{{ __('dashboard.Item_Name') }}</label>
                                    <input type="text" name="item_name" class="form-control">
                                    <span class="text-danger error-text item_name_error"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <label>{{ __('dashboard.item_quantity') }}</label>
                                    <input type="number" name="item_quantity" class="form-control">
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label>{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control">
                                        <option selected disabled>{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text branch_id_error"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('dashboard.amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control">
                                <span class="text-danger error-text amount_error"></span>
                            </div>

                            <div class="mb-3">
                                <label>{{ __('dashboard.purchase_date') }}</label>
                                <input type="date" name="expense_date" class="form-control">
                                <span class="text-danger error-text expense_date_error"></span>
                            </div>

                            <div class="mb-3">
                                <label>{{ __('dashboard.description') }}</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                                <span class="text-danger error-text description_error"></span>
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_expense') }}</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  ">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteExpenseModalLabel">{{ __('dashboard.confirm_delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" id="confirmDeleteExpense"
                            class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/page/datatables.js') }}"></script>
    <script>
        $(function() {
            // Setup CSRF for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Add Expense
            $(document).on('submit', '#addExpenseForm', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true);

                let formData = new FormData(this); // For file upload

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: 'json',

                    success: function(res) {
                        if (res.success) {

                            $('#addExpenseModal').modal('hide'); // Close modal
                            $form[0].reset(); // Reset form
                            location.reload(); // Reload page to reflect new expense
                            $('#noExpenseRow').remove(); // Remove placeholder row
                            appendExpenseRow(res.data); // Add new row dynamically

                            //  Success SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Expense Added/ تمت إضافة النفقات',
                                text: res.message || 'Expense added successfully!',
                                timer: 1800,
                                showConfirmButton: false
                            });

                        }
                    },

                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Clear all previous errors
                            $form.find('.error-text').text('');

                            // Loop through errors
                            $.each(errors, function(key, messages) {
                                let parts = key.split('.');
                                let fieldName = parts[0];
                                let index = parts[1];

                                let $inputGroup = $('#itemsContainer .expense-item').eq(
                                    index);
                                $inputGroup.find(`.${fieldName}_error`).text(messages[
                                    0]);
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Something went wrong!'
                            });
                        }
                    },

                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });


            $(document).on('click', '.editExpenseBtn', function(e) {
                e.preventDefault();
                const btn = $(this);

                $('#editExpenseModal').modal('show');

                // Populate modal fields
                $('#editExpenseForm input[name="id"]').val(btn.data('id'));
                $('#editExpenseForm input[name="item_name"]').val(btn.data('item_name'));
                $('#editExpenseForm input[name="invoice_number"]').val(btn.data('invoiceNumber'));
                $('#editExpenseForm input[name="item_quantity"]').val(btn.data('quantity'));
                $('#editExpenseForm select[name="branch_id"]').val(btn.data('branch'));
                $('#editExpenseForm input[name="amount"]').val(btn.data('amount'));
                $('#editExpenseForm input[name="expense_date"]').val(btn.data('expense_date'));
                $('#editExpenseForm textarea[name="description"]').val(btn.data('description'));
            });


            // Update Expense via AJAX
            $(document).on('submit', '#editExpenseForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('dashboard.finance.expense.update') }}',
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#editExpenseModal').modal('hide');

                            // Update the row dynamically
                            updateExpenseRow(res.data);

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!/تم التحديث',
                                text: res.message || 'Expense updated successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Function to update row dynamically
            function updateExpenseRow(expense) {
                const row = $('#expenseRow' + expense.id);

                if (row.length) {
                    row.find('td:nth-child(1)').text(expense.item_name);
                    row.find('td:nth-child(2)').text(expense.branch_name);
                    row.find('td:nth-child(3)').text(parseFloat(expense.amount).toFixed(2));
                    row.find('td:nth-child(4)').text(expense.expense_date);
                    row.find('td:nth-child(5)').text(expense.description ?? '');

                    // Update data-* attributes of edit button
                    const editBtn = row.find('.editExpenseBtn');
                    editBtn.data('item_name', expense.item_name);
                    editBtn.data('amount', expense.amount);
                    editBtn.data('branch', expense.branch_id);
                    editBtn.data('expense_date', expense.expense_date);
                    editBtn.data('description', expense.description);
                }
            }



            function appendExpenseRow(expense) {
                const html = `
<tr id="expenseRow${expense.id}">
    <td>${expense.item_name}</td>
    <td>${expense.branch_name ?? ''}</td>
    <td>${parseFloat(expense.amount).toFixed(2)}</td>
    <td>${expense.expense_date}</td>
    <td>${expense.description ?? ''}</td>
    <td>
        <a href="#" class="text-secondary editExpenseBtn"
            data-id="${expense.id}"
            data-branch="${expense.branch_id}"
            data-item_name="${expense.item_name}"
            data-amount="${expense.amount}"
            data-expense_date="${expense.expense_date}"
            data-description="${expense.description}">
            <i class="fas fa-edit"></i>
        </a>
        <a href="#" class="text-danger deleteExpenseBtn" data-id="${expense.id}">
            <i class="fas fa-trash-alt"></i>
        </a>
    </td>
</tr>`;

                $('#tableExport tbody').prepend(html);
            }




        });
        let deleteExpenseId = null;

        // When user clicks delete icon
        $(document).on('click', '.deleteExpenseBtn', function(e) {
            e.preventDefault();
            deleteExpenseId = $(this).data('id'); // store ID temporarily
            $('#deleteExpenseModal').modal('show');
        });

        // When user confirms delete in modal
        $('#confirmDeleteExpense').on('click', function() {
            if (!deleteExpenseId) return;

            $.ajax({
                url: '/dashboard/finance/expense/delete/' + deleteExpenseId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        // Hide the modal
                        $('#deleteExpenseModal').modal('hide');

                        // Remove the row dynamically
                        $('#expenseRow' + deleteExpenseId).fadeOut(400, function() {
                            $(this).remove();
                        });

                        //  SweetAlert success popup
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!/تم الحذف',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!/خطأ',
                            text: res.message || 'Something went wrong!'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!/فشل',
                        text: xhr.responseJSON?.message || 'Failed to delete expense.'
                    });
                }
            });
        });

        $(function() {
            function fetchExpenses(page = 1) {
                const data = {
                    branch_id: $('#filter_branch').val(),
                    item_name: $('#filter_item').val(),
                    start_date: $('#filter_start').val(),
                    end_date: $('#filter_end').val(),
                    page: page
                };

                $.ajax({
                    url: "{{ route('expenses.filter') }}",
                    data: data,
                    beforeSend: function() {
                        $('#expenseTableBody').html(
                            '<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                    },
                    success: function(res) {
                        $('#expenseTableBody').html(res.html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText || 'AJAX error');
                        $('#expenseTableBody').html(
                            '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>'
                        );
                    }
                });
            }

            // Filter button
            $('#filterBtn').on('click', function() {
                fetchExpenses();
            });

        });
        $('#addMoreItem').click(function() {
            // Create a new row for item name and quantity only
            let newItem = `
    <div class="expense-item border rounded p-3 mb-4 bg-light">
        <h6 class="mb-3 fw-bold">{{ __('dashboard.expense_item') }}</h6>
        <div class="row">
            <div class="form-group col-md-6 mb-3">
                <label>{{ __('dashboard.Item_Name') }}</label>
                <input type="text" name="name[]" class="form-control">
            </div>
            <div class="form-group col-md-6 mb-3">
                <label>{{ __('dashboard.item_quantity') }}</label>
                <input type="number" name="item_quantity[]" class="form-control">
            </div>
        </div>
    </div>`;

            $('#itemsContainer').append(newItem);
        });
    </script>
@endsection
