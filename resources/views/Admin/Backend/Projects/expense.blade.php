@extends('layout.master')
@section('title', 'Dashboard | Project-Expense')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_expenses') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.expenses') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addProjectExpenseModal">
                                    {{ __('dashboard.add_expense') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.project_name') }}</th>
                                                <th>{{ __('dashboard.expense_date') }}</th>
                                                <th>{{ __('dashboard.amount') }}</th>
                                                <th>{{ __('dashboard.category') }}</th>
                                                <th>{{ __('dashboard.documents') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($expenses as $expense)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $expense->project->name ?? '-' }}</td>
                                                    <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') : '-' }}
                                                    </td>
                                                    <td>{{ $expense->amount ? number_format($expense->amount, 2) : '-' }}
                                                    </td>
                                                    <td>{{ $expense->category ?? '-' }}</td>
                                                    <td>
                                                        @if ($expense->documents)
                                                            @php
                                                                $docs = json_decode($expense->documents, true);
                                                            @endphp
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($docs as $doc)
                                                                    <div class="me-2 mb-1">
                                                                        <a href="{{ asset('storage/' . $doc) }}"
                                                                            target="_blank" title="{{ basename($doc) }}">
                                                                            <i
                                                                                class="fas fa-file-alt fa-lg text-secondary"></i>
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editExpenseModal{{ $expense->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteExpenseModal_{{ $expense->id }}">
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


        <!-- Add Project Expense Modal -->
        <div class="modal fade" id="addProjectExpenseModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <form action="{{ route('dashboard.company.expense.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('dashboard.add_project_expense') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <div class="form-row">

                                <!-- Project -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.project') }} <span class="text-danger">*</span></label>
                                    <select name="project_id" class="form-control" required>
                                        <option value="">{{ __('dashboard.select_project') }}</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Expense Date -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.expense_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="expense_date" class="form-control"
                                        value="{{ old('expense_date') }}" required>
                                    @error('expense_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-row">

                                <!-- Amount -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control"
                                        value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.category') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="category" class="form-control"
                                        value="{{ old('category') }}" required>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <!-- Documents -->
                            <div class="form-group">
                                <label for="documents">{{ __('dashboard.documents') }}</label>
                                <div class="custom-file">
                                    <input type="file" name="documents[]" id="documents" class="custom-file-input"
                                        multiple>
                                    <label class="custom-file-label"
                                        for="documents">{{ __('dashboard.choose_files') }}</label>
                                </div>
                                @error('documents')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>



        @foreach ($expenses as $expense)
            <!-- Edit Project Expense Modal -->
            <div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <form action="{{ route('dashboard.company.expense.update', $expense->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-content">

                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('dashboard.edit_project_expense') }}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body">
                                <div class="row">

                                    <!-- Project Select -->
                                    <div class="col-md-6 mb-3">
                                        <label for="project_id">{{ __('dashboard.project_name') }}</label>
                                        <select name="project_id" id="project_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_project') }}</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    {{ $expense->project_id == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Expense Date -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.expense_date') }}</label>
                                        <input type="date" name="expense_date" class="form-control"
                                            value="{{ $expense->expense_date }}" required>
                                        @error('expense_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Amount -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.amount') }}</label>
                                        <input type="number" step="0.01" name="amount" class="form-control"
                                            value="{{ $expense->amount }}" required>
                                        @error('amount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-6 mb-3">
                                        <label>{{ __('dashboard.category') }}</label>
                                        <input type="text" name="category" class="form-control"
                                            value="{{ $expense->category }}" required>
                                        @error('category')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Existing Documents -->
                                    <div class="col-md-12 mb-3">
                                        <label>{{ __('dashboard.existing_documents') }}</label>
                                        <div class="d-flex flex-wrap mb-2">
                                            @if ($expense->documents)
                                                @foreach (json_decode($expense->documents, true) as $doc)
                                                    <div class="me-2 mb-1">
                                                        <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                                            title="{{ basename($doc) }}">
                                                            <i class="fas fa-file-alt fa-lg text-secondary"></i>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Add New Documents -->
                                    <div class="col-md-12 mb-3">
                                        <label>{{ __('dashboard.add_new_documents') }}</label>
                                        <div class="custom-file">
                                            <input type="file" name="documents[]" class="custom-file-input" multiple>
                                            <label class="custom-file-label"
                                                for="documents">{{ __('dashboard.choose_files') }}</label>
                                        </div>
                                        @error('documents')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        @endforeach


        {{-- Project Expense Delete Modals --}}
        @foreach ($expenses as $expense)
            <div class="modal fade" id="deleteExpenseModal_{{ $expense->id }}" tabindex="-1"
                aria-labelledby="deleteExpenseModalLabel_{{ $expense->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteExpenseModalLabel_{{ $expense->id }}">
                                {{ __('dashboard.delete_project_expense') }}
                            </h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('dashboard.company.expense.destroy', $expense->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $expense->category }} - {{ $expense->amount }}</strong>?
                                </p>
                                @if ($expense->documents)
                                    <p class="text-warning">
                                        <small>{{ __('dashboard.all_docs_will_be_deleted') }}</small>
                                    </p>
                                @endif
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach



    </div>
    <script>
        @if ($errors->any())
            $(document).ready(function() {
                $('#addProjectExpenseModal').modal('show');
            });
        @endif

        $('.custom-file-input').on('change', function() {
            let fileNames = Array.from(this.files).map(f => f.name).join(', ');
            $(this).next('.custom-file-label').html(fileNames);
        });
    </script>



@endsection
