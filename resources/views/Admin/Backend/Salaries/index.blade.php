@extends('layout.master')
@section('title', 'Dashboard | Salaries')
@section('main')

    <div class="main-content">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ __('dashboard.branch_base_salary') }}</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('finance.branch-salaries.view') }}" method="GET">

                    <div class="row g-3 align-items-end">



                        <!-- Branch Dropdown -->
                        <div class="col-md-3">
                            <label for="branch_id" class="form-label">{{ __('dashboard.branch_name') }}</label>
                            <select name="branch_id" id="branch_id" class="form-control" required>
                                <option value="">{{ __('dashboard.select_branch') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Input -->
                        <div class="col-md-2">
                            <label for="month" class="form-label">{{ __('dashboard.month') }}</label>
                            <input type="month" name="month" id="month" class="form-control"
                                value="{{ date('Y-m') }}" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> {{ __('dashboard.view') }}
                            </button>
                        </div>

                    </div>
                </form>

            </div>

        </div>
        <hr class="my-4">

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">{{ __('dashboard.branches_with_pending_salaries') }}</h5>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.branch_name') }}</th>
                            <th>{{ __('dashboard.pending_salaries_count') }}</th>
                            <th>{{ __('dashboard.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($unpaidBranches as $branch)
                            @if ($branch->pending_salaries_count > 0)
                                <tr>
                                    <td>{{ $branch->name }}</td>
                                    <td><span class="badge bg-danger">{{ $branch->pending_salaries_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('finance.branch-salaries.view', ['branch_id' => $branch->id, 'month' => date('Y-m')]) }}"
                                            class="btn btn-sm btn-primary">
                                            {{ __('dashboard.view_salaries') }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $unpaidBranches->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>



@endsection
