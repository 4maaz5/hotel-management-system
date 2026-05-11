@extends('layout.master')
@section('title', 'Dashboard | Income')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center">{{ __('dashboard.income_records') }}</h1>

        <div class="income-grid">
            @forelse($incomeCards as $income)
                <div class="income-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ __('dashboard.branch') }}:</strong>
                                {{ $income->branch->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong> {{ ucfirst($income->type) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.amount') }}:</strong>
                                {{ number_format($income->amount, 2) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.payment_type') }}:</strong>
                                {{ $income->payment_type ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong> {{ $income->income_date }}</p>


                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    {{-- <p>No income records found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $incomeCards->links('pagination::bootstrap-5') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.income') }}</h4>
                                <div>

                                </div>
                                <!-- Add Attendance Button -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addIncomeModal">
                                    {{ __('dashboard.add_income') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="incomeFilterForm" class="mb-3">
                                        <div class="row g-2">

                                            <div class="col-md-2">
                                                <select id="filter_branch" name="branch_id" class="form-control">
                                                    <option value="">{{ __('dashboard.all_branches') }}</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <input id="filter_type" type="text" name="type" class="form-control"
                                                    placeholder="{{ __('dashboard.income_type') }}">
                                            </div>

                                            <div class="col-md-2">
                                                <input id="filter_start" type="date" name="start_date"
                                                    class="form-control" placeholder="{{ __('dashboard.start_date') }}">
                                            </div>

                                            <div class="col-md-2">
                                                <input id="filter_end" type="date" name="end_date" class="form-control"
                                                    placeholder="{{ __('dashboard.end_date') }}">
                                            </div>

                                            <div class="col-md-2">
                                                <input id="payment_filter" type="text" name="payment_type"
                                                    class="form-control" placeholder="{{ __('dashboard.payment_type') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" id="filterBtn"
                                                    class="btn btn-primary w-100">{{ __('dashboard.filter') }}</button>
                                            </div>

                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.employee_name') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.amount') }}</th>
                                                <th>{{ __('dashboard.payment_type') }}</th>
                                                <th>{{ __('dashboard.date') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody id="IncomeFilter">
                                            @include('Admin.Backend.partials.incomes_rows', [
                                                'incomes' => $incomes,
                                            ])
                                        </tbody>


                                    </table>
                                    <div id="incomePagination"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Income Modal -->
        <div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_income') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('dashboard.finance.income.store') }}" method="POST">
                        @csrf

                        <div class="modal-body">

                            <div class="form-row">

                                {{-- Branch Selection for Super Admin --}}
                                @if (Auth::user()->hasRole('super_admin'))
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
                                @else
                                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                                @endif

                                {{-- Income Type --}}
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.income_type') }} <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option selected disabled>{{ __('dashboard.select_type') }}</option>
                                        <option value="booking">{{ __('dashboard.booking') }}</option>
                                        <option value="food">{{ __('dashboard.food_beverage') }}</option>
                                        <option value="laundry">{{ __('dashboard.laundry') }}</option>
                                        <option value="hall">{{ __('dashboard.hall') }}</option>
                                        <option value="other">{{ __('dashboard.other') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- NEW: Select Salesman / Employee --}}
                            <div class="form-group">
                                <label>{{ __('dashboard.salesman') }}</label>
                                <select name="employee_id" class="form-control">
                                    <option value="" selected>{{ __('dashboard.no_employee') }}</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('dashboard.if_selected') }}</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.payment_type') }}</label>
                                    <select name="payment_type" class="form-control">
                                        <option selected disabled>{{ __('dashboard.select_payment_method') }}</option>
                                        <option value="cash">{{ __('dashboard.cash') }}</option>
                                        <option value="card">{{ __('dashboard.card') }}</option>
                                        <option value="transfer">{{ __('dashboard.bank_transfer') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="income_date" class="form-control" required>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save_income') }}</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>




        <!-- Edit Income Modal -->
        <div class="modal fade" id="editIncomeModal" tabindex="-1" role="dialog"
            aria-labelledby="editIncomeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editIncomeModalLabel">{{ __('dashboard.edit_income') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editIncomeForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="editIncomeId">

                        <div class="modal-body">
                            <div class="form-row">

                                {{-- Branch (Only for Super Admin) --}}
                                @if (Auth::user()->hasRole('super_admin'))
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{ __('dashboard.branch') }}:</label>
                                        <label
                                            class="form-label">{{ __('dashboard.company') }}→{{ __('dashboard.brand') }}→{{ __('dashboard.branch') }}</label>
                                        <select name="branch_id" class="form-control" required id="branch_id">
                                            <option value="">-- {{ __('dashboard.select_branch') }} --</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">
                                                    {{ $branch->company->name ?? 'N/A' }} →
                                                    {{ $branch->brand->name ?? 'N/A' }} → {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="branch_id" id="editBranchId"
                                        value="{{ Auth::user()->branch_id }}">
                                @endif

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.income_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="type" id="editType" required>
                                        <option value="booking">{{ __('dashboard.booking') }}</option>
                                        <option value="food">{{ __('dashboard.food_beverage') }}</option>
                                        <option value="laundry">{{ __('dashboard.laundry') }}</option>
                                        <option value="hall">{{ __('dashboard.hall') }}</option>
                                        <option value="other">{{ __('dashboard.other') }}</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="amount"
                                        id="editAmount" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.payment_type') }}</label>
                                    <select class="form-control" name="payment_type" id="editPaymentType">
                                        <option value="cash">{{ __('dashboard.cash') }}</option>
                                        <option value="card">{{ __('dashboard.card') }}</option>
                                        <option value="transfer">{{ __('dashboard.bank_transfer') }}</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="income_date" id="editIncomeDate"
                                    required>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update_income') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <!-- Delete Income Modal -->
        <div class="modal fade" id="deleteIncomeModal" tabindex="-1" aria-labelledby="deleteIncomeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteIncomeModalLabel">{{ __('dashboard.delete_income') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="deleteIncomeForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteIncomeId" name="id">

                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                            <p>
                                <strong>{{ __('dashboard.type') }}:</strong> <span id="deleteIncomeType"></span><br>
                                <strong>{{ __('dashboard.amount') }}:</strong> <span id="deleteIncomeAmount"></span>
                            </p>
                        </div>

                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                        </div>
                    </form>

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
        $(document).ready(function() {
            $('#addIncomeModal form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = "{{ route('dashboard.finance.income.store') }}"; // your store route
                let formData = form.serialize();

                // Clear old validation messages and invalid styles
                form.find('.text-danger').remove();
                form.find('.is-invalid').removeClass('is-invalid');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            // Reset form and close modal
                            form[0].reset();
                            $('#addIncomeModal').modal('hide');

                            // SweetAlert popup like Edit
                            Swal.fire({
                                icon: 'success',
                                title: 'Added!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Reload table only
                            $('#tableExport').load(location.href + ' #tableExport');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 || xhr.status === 409) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, messages) {
                                let input = form.find(`[name="${key}"]`);

                                if (input.length > 0) {
                                    input.addClass('is-invalid');
                                    input.after(
                                        `<span class="text-danger">${messages[0]}</span>`
                                    );
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "Something went wrong while adding income!"
                            });
                        }
                    }
                });
            });
        });


        // Fill Edit Income Modal
        $(document).on('click', '.edit-income-btn', function() {
            let income = $(this).data();
            var branch_id = $(this).data('branch_id');

            $('#editIncomeId').val(income.id);
            $('#editBranchId').val(income.branch_id);
            $('#editIncomeModal #branch_id').val(branch_id);
            $('#editType').val(income.type);
            $('#editAmount').val(income.amount);
            $('#editPaymentType').val(income.payment_type);
            $('#editIncomeDate').val(income.income_date);
        });



        $(document).on('submit', '#editIncomeForm', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let id = $('#editIncomeId').val();

            $.ajax({
                url: "{{ url('dashboard/finance/income/update') }}/" + id, // full absolute URL
                type: "POST",
                data: formData + "&_method=PUT", // includes _method=PUT
                success: function(response) {
                    if (response.status === "success") {

                        $('#editIncomeModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Reload table only
                        $('#tableExport').load(location.href + ' #tableExport');
                    }
                },

                error: function(xhr) {
                    form.find(".text-danger").remove();

                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, messages) {
                            let input = form.find(`[name="${key}"]`);
                            if (input.length) {
                                input.after(`<span class="text-danger">${messages[0]}</span>`);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "Something went wrong while updating income!"
                        });
                    }
                }
            });
        });


        // Show Delete Modal with Data
        $(document).on('click', '.delete-income-btn', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let amount = $(this).data('amount');

            $('#deleteIncomeId').val(id);
            $('#deleteIncomeType').text(type);
            $('#deleteIncomeAmount').text(amount);
        });

        // Handle Delete Form Submission
        $(document).on('submit', '#deleteIncomeForm', function(e) {
            e.preventDefault();

            let id = $('#deleteIncomeId').val();

            $.ajax({
                url: "/dashboard/finance/income/delete/" + id, // your delete route
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#deleteIncomeModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Reload table dynamically
                        $('#tableExport').load(location.href + ' #tableExport');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong while deleting income.'
                    });
                }
            });
        });


        $(function() {
            function fetchIncomes(page = 1) {
                const data = {
                    branch_id: $('#filter_branch').val(),
                    type: $('#filter_type').val(),
                    start_date: $('#filter_start').val(),
                    end_date: $('#filter_end').val(),
                    payment_type: $('#payment_filter').val(),
                    page: page
                };

                $.ajax({
                    url: "{{ route('income.filter') }}",
                    data: data,
                    beforeSend: function() {
                        $('#IncomeFilter').html(
                            '<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    },
                    success: function(res) {
                        $('#IncomeFilter').html(res.html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('#filterBtn').on('click', function() {
                fetchIncomes();
            });
        });
    </script>
@endsection
