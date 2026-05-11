@extends('layout.master')
@section('title', 'Dashboard | Budget')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center">{{ __('dashboard.branch_budgets') }}</h1>

        <div class="cards-grid budgets-grid">
            @forelse($budgetCards as $budget)
                <div class="card-item">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ __('dashboard.branch_id') }}:</strong> {{ $budget->branch->id }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.branch_name') }}:</strong> {{ $budget->branch->name }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.allocated_budget') }}:</strong>
                                {{ number_format($budget->total_budget, 2) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.used_budget') }}:</strong>
                                {{ number_format($budget->used_budget, 2) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.remaining_budget') }}:</strong>
                                {{ number_format($budget->remaining_budget, 2) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.period') }}:</strong>
                                @php
                                    $start = \Carbon\Carbon::parse($budget->start_date);
                                    $end = \Carbon\Carbon::parse($budget->end_date);

                                    $totalDays = $start->diffInDays($end);
                                    $months = intdiv($totalDays, 30);
                                    $days = $totalDays % 30;

                                    if ($months > 0 && $days > 0) {
                                        $duration = "{$months} month(s) {$days} day(s)";
                                    } elseif ($months > 0) {
                                        $duration = "{$months} month(s)";
                                    } else {
                                        $duration = "{$days} day(s)";
                                    }
                                @endphp
                                {{ $duration }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.status') }}:</strong>
                                @php
                                    $status = $budget->status;
                                    $badgeClass = match ($status) {
                                        'On Track' => 'bg-success text-white',
                                        'At Risk' => 'bg-warning text-white',
                                        'Over Spent' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            </p>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    {{-- <p>No budgets found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $budgetCards->links('pagination::bootstrap-5') }}
        </div>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.budget_management') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addBudgetModal">
                                    {{ __('dashboard.add_budget') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="budgetFilterForm" class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <select id="filter_branch" name="branch_id" class="form-control">
                                                    <option value="">{{ __('dashboard.all_branches') }}</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <select id="filter_status" name="status" class="form-control">
                                                    <option value="">{{ __('dashboard.all_status') }}</option>
                                                    <option value="On Track">On Track/على المسار الصحيح</option>
                                                    <option value="At Risk">At Risk/معرض للخطر</option>
                                                    <option value="Over Spent">Over Spent/إنفاق مبالغ فيه</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <input type="date" id="filter_start" name="start_date"
                                                    class="form-control" placeholder="{{ __('dashboard.start_date') }}">
                                            </div>

                                            <div class="col-md-2">
                                                <input type="date" id="filter_end" name="end_date" class="form-control"
                                                    placeholder="{{ __('dashboard.end_date') }}">
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
                                                <th>{{ __('dashboard.branch_id') }}</th>
                                                <th>{{ __('dashboard.branch_name') }}</th>
                                                <th>{{ __('dashboard.allocated_budget') }}</th>
                                                <th>{{ __('dashboard.used_budget') }}</th>
                                                <th>{{ __('dashboard.remaining_budget') }}</th>
                                                <th>{{ __('dashboard.period') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="BudgetFilterBody">
                                            @forelse ($budgets as $budget)
                                                <tr data-id="{{ $budget->id }}">
                                                    <td>{{ $budget->branch->id }}</td>
                                                    <td>{{ $budget->branch->name }}</td>
                                                    <td>{{ number_format($budget->total_budget, 2) }}</td>
                                                    <td>{{ number_format($budget->used_budget, 2) }}</td>
                                                    <td>{{ number_format($budget->remaining_budget, 2) }}</td>
                                                    <td>
                                                        @php
                                                            $start = \Carbon\Carbon::parse($budget->start_date);
                                                            $end = \Carbon\Carbon::parse($budget->end_date);
                                                            $totalDays = $start->diffInDays($end);
                                                            $months = intdiv($totalDays, 30);
                                                            $days = $totalDays % 30;
                                                            $duration =
                                                                $months > 0
                                                                    ? "{$months} month(s) {$days} day(s)"
                                                                    : "{$days} day(s)";
                                                        @endphp
                                                        {{ $duration }}
                                                    </td>
                                                <td>
    @php
        $status = $budget->status;

        $badgeClass = match ($status) {
            'On Track'   => 'bg-success text-white',
            'At Risk'    => 'bg-warning text-white',
            'Over Spent' => 'bg-danger text-white',
            default      => 'bg-secondary text-white',
        };

        $translations = [
            'On Track'   => __('dashboard.on_track'),
            'At Risk'    => __('dashboard.at_risk'),
            'Over Spent' => __('dashboard.over_spent'),
        ];
    @endphp

    <span class="badge {{ $badgeClass }}">
        {{ $translations[$status] ?? $status }}
    </span>
</td>

                                                    <td>
                                                        <a href="#" class="text-secondary me-2 edit-btn"
                                                            data-id="{{ $budget->id }}"><i class="fas fa-edit"></i></a>
                                                        <a href="#" class="text-danger delete-btn"
                                                            data-id="{{ $budget->id }}"><i
                                                                class="fas fa-trash-alt"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    {{-- <td colspan="8" class="text-center">No budgets found.</td> --}}
                                                </tr>
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

        <!-- Add Budget Modal -->
        <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBudgetModalLabel">{{ __('dashboard.add_budget') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form action="{{ route('dashboard.finance.budget.store') }}" method="post">
                            @csrf

                            <!-- Branch -->
                            <div class="row">
                                <div class="form-group col-md-12">
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
                            <!-- Used Budget & Remaining Budget -->
                            <div class="row">
                                <!-- Allocated Budget -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.allocated_budget') }}</label>
                                    <input type="number" class="form-control" placeholder="0.00" name="total_budget">
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.used_budget') }}</label>
                                    <input type="number" class="form-control" placeholder="0.00" name="used_budget">
                                </div>

                            </div>

                            <!-- Period / Fiscal Year -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.start_date') }}</label>
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label">{{ __('dashboard.end_date') }}</label>
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-control">
                                    <option selected disabled>{{ __('dashboard.select_status') }}</option>
                                    <option value="On Track">{{ __('dashboard.on_track') }}</option>
                                    <option value="At Risk">{{ __('dashboard.at_risk') }}</option>
                                    <option value="Over Spent">{{ __('dashboard.over_spent') }}</option>
                                </select> 
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-end">

                                <button type="submit" class="btn btn-primary">{{ __('dashboard.save_budget') }}</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Budget Modal -->
        <div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editBudgetModalLabel">{{ __('dashboard.edit_budget') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editBudgetForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="edit_budget_id" name="id">

                            <!-- Branch -->
                            <div class="form-group">
                                <label>{{ __('dashboard.branch') }}</label>
                                <select class="form-control" name="branch_id" id="edit_branch_id" required>
                                    <option disabled>{{ __('dashboard.select_branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Budgets -->
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.allocated_budget') }}</label>
                                    <input type="number" class="form-control" name="total_budget"
                                        id="edit_total_budget">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.used_budget') }}</label>
                                    <input type="number" class="form-control" name="used_budget" id="edit_used_budget">
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.start_date') }}</label>
                                    <input type="date" class="form-control" name="start_date" id="edit_start_date">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.end_date') }}</label>
                                    <input type="date" class="form-control" name="end_date" id="edit_end_date">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label>{{ __('dashboard.status') }}</label>
                                <select class="form-control" name="status" id="edit_status">
                                    <option disabled>{{ __('dashboard.select_status') }}</option>
                                    <option value="On Track">{{ __('dashboard.on_track') }}</option>
                                    <option value="At Risk">{{ __('dashboard.at_risk') }}</option>
                                    <option value="Over Spent">{{ __('dashboard.over_spent') }}</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_budget') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteBudgetModal" tabindex="-1" aria-labelledby="deleteBudgetModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteBudgetModalLabel">{{ __('dashboard.confirm_delete_modal') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete') }}</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" id="confirmDeleteBudget"
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
      window.budgetStatusTranslations = {
        "On Track": "{{ __('dashboard.on_track') }}",
        "At Risk": "{{ __('dashboard.at_risk') }}",
        "Over Spent": "{{ __('dashboard.over_spent') }}"
    };
        $(document).ready(function() {

            $('#addBudgetModal form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('dashboard.finance.budget.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!/تم بنجاح!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#addBudgetModal').modal('hide');

                            let b = response.budget;

                            // Calculate period duration
                            const start = new Date(b.start_date);
                            const end = new Date(b.end_date);
                            const diffTime = Math.abs(end - start);
                            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                            const months = Math.floor(diffDays / 30);
                            const days = diffDays % 30;
                            let duration = '';
                            if (months > 0 && days > 0) {
                                duration = `${months} month(s) ${days} day(s)`;
                            } else if (months > 0) {
                                duration = `${months} month(s)`;
                            } else {
                                duration = `${days} day(s)`;
                            }

                            $('#tableExport tbody').append(`
                    <tr data-id="${b.id}">
                        <td>${b.branch.id}</td>
                        <td>${b.branch.name}</td>
                        <td>${b.total_budget}</td>
                        <td>${b.used_budget}</td>
                        <td>${b.remaining_budget}</td>
                        <td>${duration}</td>
                      <td>
    <span class="badge ${
        b.status === 'On Track' ? 'bg-success text-white' :
        b.status === 'At Risk' ? 'bg-warning text-white' :
        'bg-danger text-white'
    }">
        ${window.budgetStatusTranslations[b.status] ?? b.status}
    </span>
</td>
<td>
                            <a href="#" class="text-secondary edit-btn" data-id="${b.id}" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="text-danger delete-btn" data-id="${b.id}" title="Delete"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                `);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message ||
                            'Failed to add budget./فشل في إضافة الميزانية.',
                            'error');
                    }
                });
            });


            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');

                $.get(`/dashboard/finance/budget/${id}/edit`, function(data) {
                    // Populate the modal fields
                    $('#edit_budget_id').val(data.id);
                    $('#edit_branch_id').val(data.branch_id);
                    $('#edit_total_budget').val(data.total_budget);
                    $('#edit_used_budget').val(data.used_budget);
                    $('#edit_start_date').val(data.start_date);
                    $('#edit_end_date').val(data.end_date);
                    $('#edit_status').val(data.status);

                    // Show the modal
                    $('#editBudgetModal').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Failed to fetch budget details./فشل في إضافة الميزانية',
                        'error');
                });
            });


            $('#editBudgetModal form').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_budget_id').val(); // get the actual budget ID
                let formData = new FormData(this);

                $.ajax({
                    url: `/dashboard/finance/budget/${id}`, // <-- use template literal
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT' // Laravel recognizes this as PUT
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Updated!/تم التحديث!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#editBudgetModal').modal('hide');

                            const b = response.budget;
                            const row = $(`#tableExport tbody tr[data-id="${b.id}"]`);

                            if (row.length) {
                                row.find('td:eq(1)').text(b.branch.name);
                                row.find('td:eq(2)').text(b.total_budget);
                                row.find('td:eq(3)').text(b.used_budget);
                                row.find('td:eq(4)').text(b.remaining_budget);

                                // Calculate and update period
                                const start = new Date(b.start_date);
                                const end = new Date(b.end_date);
                                const diffTime = Math.abs(end - start);
                                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                                const months = Math.floor(diffDays / 30);
                                const days = diffDays % 30;
                                let duration = '';
                                if (months > 0 && days > 0) {
                                    duration = `${months} month(s) ${days} day(s)`;
                                } else if (months > 0) {
                                    duration = `${months} month(s)`;
                                } else {
                                    duration = `${days} day(s)`;
                                }
                                row.find('td:eq(5)').text(duration);

                                const badge = row.find('td:eq(6) span');

const statusClasses = {
    'On Track': 'bg-success text-white',
    'At Risk': 'bg-warning text-white',
    'Over Spent': 'bg-danger text-white'
};

const statusText = window.budgetStatusTranslations[b.status] ?? b.status;

badge
    .text(statusText)
    .removeClass()
    .addClass(`badge ${statusClasses[b.status] ?? 'bg-secondary text-white'}`);

                            }

                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseJSON);
                        Swal.fire('Error', xhr.responseJSON?.message ||
                            'Failed to update budget.', 'error');
                    }
                });
            });




            $(document).ready(function() {
                let deleteBudgetId = null;


                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    deleteBudgetId = $(this).data('id');
                    $('#deleteBudgetModal').modal('show');
                });


                $('#confirmDeleteBudget').on('click', function() {
                    if (!deleteBudgetId) return;

                    $.ajax({
                        url: `/dashboard/finance/budget/delete/${deleteBudgetId}`,
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {

                                $('#deleteBudgetModal').modal('hide');


                                $(`#tableExport tbody tr[data-id="${deleteBudgetId}"]`)
                                    .remove();
                                Swal.fire({
                                    title: 'Deleted!/تم الحذف!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Error',
                                'Failed to delete budget./فشل في حذف الميزانية',
                                'error');
                        }
                    });
                });
            });

        });

        $(function() {
            function fetchBudgets() {
                const data = {
                    branch_id: $('#filter_branch').val(),
                    status: $('#filter_status').val(),
                    start_date: $('#filter_start').val(),
                    end_date: $('#filter_end').val()
                };

                $.ajax({
                    url: "{{ route('budgets.filter') }}",
                    data: data,
                    beforeSend: function() {
                        $('#BudgetFilterBody').html(
                            '<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                    },
                    success: function(res) {
                        $('#BudgetFilterBody').html(res.html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('#filterBtn').on('click', function() {
                fetchBudgets();
            });
        });
    </script>


@endsection
