@extends('layout.master')
@section('title', 'Dashboard | Finance')
@section('main')

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="row ">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.total_income') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalIncome }} SAR</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/average1.png') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.admin_expense') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalExpenses }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/pending1.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.payroll_cost') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $payrollCost }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/salary1.avif') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.pending_payrolls') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $pendingTransactions }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/percent1.png') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> {{ __('dashboard.income_chart') }}
                    </h4>

                    <select id="incomeFilter" class="form-control" style="width:200px;">
                        <option value="daily">{{ __('dashboard.daily') }}</option>
                        <option value="weekly">{{ __('dashboard.weekly') }}</option>
                        <option value="monthly" selected>{{ __('dashboard.monthly') }}</option>
                        <option value="yearly">{{ __('dashboard.yearly') }}</option>
                    </select>
                </div>

                <div class="card-body">
                    <canvas id="incomeChart" height="300"></canvas>
                </div>
            </div>

            <h1 class="text-center">{{ __('dashboard.transactions') }}</h1>

            <div class="transactions-grid">
                @forelse($transactionCards as $transaction)
                    <div class="transaction-card">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong> {{ $transaction->type }}
                                </p>
                                <p class="mb-1"><strong>{{ __('dashboard.amount') }}:</strong>
                                    {{ number_format($transaction->amount, 2) }}</p>
                                <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong>
                                    {{ $transaction->branch->name ?? '-' }}</p>
                                <p class="mb-1"><strong>{{ __('dashboard.date') }}:</strong> {{ $transaction->date }}
                                </p>
                                <p class="mb-1"><strong>{{ __('dashboard.description') }}:</strong>
                                    {{ $transaction->description ?? '-' }}</p>


                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                    </div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $transactionCards->links('pagination::bootstrap-5') }}
            </div>
            <div class="card-body" style="background-color: white;">
                <div class="table-responsive">
                    <form id="transactionFilterForm" class="mb-3">
                        <div class="row g-2">
                            <!-- same inputs as above but add IDs -->
                            <div class="col-md-2">
                                <select id="filter_type" name="type" class="form-control">
                                    <option value="">{{ __('dashboard.all_types') }}</option>

                                    <option value="Salary">{{ __('dashboard.salary') }}</option>
                                    <option value="Expense">{{ __('dashboard.expense') }}</option>
                                    <option value="Bonus">{{ __('dashboard.bonus') }}</option>
                                    <option value="Payroll">{{ __('dashboard.payroll') }}</option>
                                    <option value="Other">{{ __('dashboard.other') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter_branch" name="branch_id" class="form-control">
                                    <option value="">{{ __('dashboard.select_branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input id="filter_start" type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <input id="filter_end" type="date" name="end_date" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <input id="filter_q" type="text" name="q" class="form-control"
                                    placeholder="{{ __('dashboard.search') }}">
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
                                <th>#</th>
                                <th>{{ __('dashboard.type') }}</th>
                                <th>{{ __('dashboard.amount') }}</th>
                                <th>{{ __('dashboard.branch') }}</th>
                                <th>{{ __('dashboard.date') }}</th>
                                <th>{{ __('dashboard.description') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody">
                            @forelse ($transactions as $transaction)
                                <tr id="transactionRow{{ $transaction->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $transaction->type }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->branch->name ?? '-' }}</td>
                                    <td>{{ $transaction->date }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        <a href="#" class="text-secondary editTransactionBtn"
                                            data-id="{{ $transaction->id }}" data-type="{{ $transaction->type }}"
                                            data-branch="{{ $transaction->branch_id }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-date="{{ $transaction->date }}"
                                            data-description="{{ $transaction->description }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="#" class="text-danger deleteTransactionBtn"
                                            data-id="{{ $transaction->id }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <div id="transactionsPagination"></div>
                            @empty
                                <tr>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </section>
        <!-- Edit Transaction Modal -->
        <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTransactionModalLabel">{{ __('dashboard.edit_transaction') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="editTransactionForm">

                            <input type="hidden" id="edit_transaction_id">

                            <div class="row">
                                <!-- Transaction Type -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.transaction_type') }}</label>
                                    <select class="form-control" id="edit_transaction_type" name="type">
                                        <option disabled>{{ __('dashboard.salary_type') }}</option>
                                        <option value="Salary">{{ __('dashboard.salary') }}</option>
                                        <option value="Expense">{{ __('dashboard.expense') }}</option>
                                        <option value="Bonus">{{ __('dashboard.bonus') }}</option>
                                        <option value="Payroll">{{ __('dashboard.payroll') }}</option>
                                        <option value="Other">{{ __('dashboard.other') }}</option>
                                    </select>
                                </div>

                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.select_branch') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_branch_id" name="branch_id">
                                        <option disabled>{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Amount -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.amount') }}</label>
                                    <input type="number" class="form-control" id="edit_amount" name="amount">
                                </div>

                                <!-- Date -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.date') }}</label>
                                    <input type="date" class="form-control" id="edit_date" name="date">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Description -->
                                <div class="form-group col-md-12 mb-3">
                                    <label class="form-label">{{ __('dashboard.description') }}</label>
                                    <textarea rows="3" class="form-control" id="edit_description" name="description"></textarea>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_transaction') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteTransactionModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteTransactionModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.confirm_delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                        <input type="hidden" id="delete_transaction_id">
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" id="confirmDeleteTransaction"
                            class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                    </div>

                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            // Setup CSRF
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            // ADD TRANSACTION
            $(document).on("submit", "#addTransactionForm", function(e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                $btn.prop("disabled", true);

                $.ajax({
                    url: $form.attr("action"),
                    method: "POST",
                    data: $form.serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.success) {
                            $("#addTransactionModal").modal("hide");
                            $form[0].reset();
                            appendTransactionRow(res.data);

                            Swal.fire({
                                icon: "success",
                                title: "Added!",
                                text: res.message ||
                                    "Transaction Added successfully.",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, $form);
                    },
                    complete: function() {
                        $btn.prop("disabled", false);
                    }
                });
            });

            $(document).on('click', '.editTransactionBtn', function(e) {
                e.preventDefault();
                const data = $(this).data();

                // Populate modal fields dynamically
                $('#edit_transaction_id').val(data.id);
                $('#edit_transaction_type').val(data.type);
                $('#edit_branch_id').val(data.branch);
                $('#edit_amount').val(data.amount);
                $('#edit_date').val(data.date);
                $('#edit_description').val(data.description);

                $('#editTransactionModal').modal('show');
            });

            $(document).on('submit', '#editTransactionForm', function(e) {
                e.preventDefault();
                const id = $('#edit_transaction_id').val();
                const formData = $(this).serialize();

                $.ajax({
                    url: '/dashboard/finance/transactions/update/' + id,
                    type: 'POST', // still POST because of _method
                    data: $(this).serialize() + '&_method=PUT', // tell Laravel it's PUT
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#editTransactionModal').modal('hide');
                            updateTransactionRow(res.data);

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message ||
                                'Something went wrong while updating!'
                        });
                    }
                });
            });

            $(document).on("click", ".deleteTransactionBtn", function(e) {
                e.preventDefault();
                const id = $(this).data("id");
                $("#delete_transaction_id").val(id);
                $("#deleteTransactionModal").modal("show");
            });

            $(document).on("click", "#confirmDeleteTransaction", function() {
                const id = $("#delete_transaction_id").val();
                const url = `/dashboard/finance/transactions/delete/${id}`; // make sure this route exists

                $.ajax({
                    url: url,
                    type: "POST", // Laravel expects POST with _method DELETE
                    data: {
                        _method: "DELETE",
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(res) {
                        if (res.success) {
                            $("#deleteTransactionModal").modal("hide");
                            $("#transactionRow" + id).remove();

                            // Re-number remaining rows
                            $("#tableExport tbody tr").each(function(index) {
                                $(this).find("td:first").text(index + 1);
                            });

                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: res.message ||
                                    "Transaction deleted successfully.",
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseJSON?.message ||
                                "Something went wrong while deleting!"
                        });
                    }
                });
            });




            // HELPER FUNCTIONS
            function appendTransactionRow(t) {
                const rowCount = $("#tableExport tbody tr").length + 1; // count rows dynamically
                const html = `
        <tr id="transactionRow${t.id}">
            <td>${rowCount}</td> <!-- Row number -->
            <td class="text-start">${t.type}</td>
            <td>${parseFloat(t.amount).toFixed(2)}</td>
            <td>${t.branch_name ?? ''}</td>
            <td>${t.date}</td>
            <td>${t.description ?? ''}</td>
            <td>
                <a href="#" class="text-secondary editTransactionBtn"
                    data-id="${t.id}"
                    data-type="${t.type}"
                    data-branch="${t.branch_id}"
                    data-amount="${t.amount}"
                    data-date="${t.date}"
                    data-description="${t.description}">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="text-danger deleteTransactionBtn" data-id="${t.id}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    `;
                $("#tableExport tbody").prepend(html);

                //  Re-number all rows after adding
                $("#tableExport tbody tr").each(function(index) {
                    $(this).find("td:first").text(index + 1);
                });
            }


            // Helper function to update table row dynamically
            function updateTransactionRow(t) {
                const row = $('#transactionRow' + t.id);
                if (row.length) {
                    row.find('td:nth-child(2)').text(t.type);
                    row.find('td:nth-child(3)').text(parseFloat(t.amount).toFixed(2));
                    row.find('td:nth-child(4)').text(t.branch_name ?? '');
                    row.find('td:nth-child(5)').text(t.date);
                    row.find('td:nth-child(6)').text(t.description ?? '');

                    // Update the data-* attributes of the edit button
                    const editBtn = row.find(".editTransactionBtn");
                    editBtn.data('type', t.type);
                    editBtn.data('branch', t.branch_id);
                    editBtn.data('amount', t.amount);
                    editBtn.data('date', t.date);
                    editBtn.data('description', t.description);
                }
            }

        });

        let incomeChart;

        function loadIncomeChart(type = 'monthly') {
            fetch(`{{ route('dashboard.income.chart') }}?type=${type}`)
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(item => item.label);
                    const totals = data.map(item => item.total);

                    if (incomeChart) incomeChart.destroy();

                    const ctx = document.getElementById('incomeChart').getContext('2d');

                    // Create gradient for line fill
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.4)');
                    gradient.addColorStop(1, 'rgba(54, 162, 235, 0)');

                    incomeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels.length ? labels : ['No Data'],
                            datasets: [{
                                label: 'Income',
                                data: totals.length ? totals : [0],
                                borderWidth: 3,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(255,99,132,1)',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: gradient,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 500
                                    } // adjust based on your income range
                                }
                            }
                        }
                    });
                });
        }

        // Dropdown change
        document.getElementById('incomeFilter').addEventListener('change', function() {
            loadIncomeChart(this.value);
        });

        // Load default
        loadIncomeChart();

        $(function() {
            function fetchTransactions(page = 1) {
                const data = {
                    type: $('#filter_type').val(),
                    branch_id: $('#filter_branch').val(),
                    start_date: $('#filter_start').val(),
                    end_date: $('#filter_end').val(),
                    q: $('#filter_q').val(),
                    page: page,
                };

                $.ajax({
                    url: "{{ route('transactions.filter') }}",
                    data: data,
                    beforeSend: function() {
                        $('#transactionTableBody').html(
                            '<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    },
                    success: function(res) {
                        $('#transactionTableBody').html(res.html);

                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('#filterBtn').on('click', function() {
                fetchTransactions();
            });

            $('#resetBtn').on('click', function() {
                $('#transactionFilterForm')[0].reset();
                fetchTransactions();
            });

            $('#filterBtn').on('click', function() {
                fetchTransactions();
            });

        });
    </script>
@endsection
