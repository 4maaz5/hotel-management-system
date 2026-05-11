<style>
    @media print {

        /* Hide buttons when printing */
        #branch-report .no-print,
        #branch-report button {
            display: none !important;
        }
    }
</style>


<h5>{{ __('dashboard.employee_payrolls') }}</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('dashboard.employee_name') }}</th>
            <th>{{ __('dashboard.total_salary') }}</th>
            <th>{{ __('dashboard.net_salary') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $emp)
            <tr>
                <td>{{ $emp->first_name . ' ' . $emp->last_name }}</td>
                <td>{{ number_format($emp->payroll->sum('total_amount'), 2) }}</td>
                <td>{{ number_format($emp->payroll->sum('net_pay'), 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h5>{{ __('dashboard.incomes') }}</h5>
<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>{{ __('dashboard.type') }}</th>
            <th>{{ __('dashboard.amount') }}</th>
            <th>{{ __('dashboard.payment_type') }}</th>
            <th>{{ __('dashboard.date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($incomes as $inc)
            <tr>
                <td>{{ $inc->type }}</td>
                <td>{{ number_format($inc->amount, 2) }}</td>
                <td>{{ $inc->payment_type ?? '--' }}</td>
                <td>{{ $inc->income_date }}</td>
            </tr>
        @endforeach
    </tbody>

    @php
        $totalIncome = $incomes->sum('amount');
    @endphp

    <tfoot>
        <tr style="font-weight: bold; background: #f8f9fa;">
            <td>{{ __('dashboard.total_income') }}</td>
            <td>{{ number_format($totalIncome, 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>


<h4 class="mt-4">{{ __('dashboard.administrative_expenses') }}</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ __('dashboard.Item_Name') }}</th>
            <th>{{ __('dashboard.invoice') }} #</th>
            <th>{{ __('dashboard.quantity') }}</th>
            <th>{{ __('dashboard.amount') }}</th>
            <th>{{ __('dashboard.date') }}</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($expenses as $exp)
            <tr>
                <td>{{ $exp->item_name }}</td>
                <td>{{ $exp->invoice_number }}</td>
                <td>{{ $exp->quantity }}</td>
                <td>{{ number_format($exp->amount, 2) }}</td>
                <td>{{ $exp->expense_date }}</td>
            </tr>
        @endforeach
    </tbody>

    @php
        $totalExpenses = $expenses->sum('amount');
    @endphp

    <tfoot>
        <tr style="font-weight:bold; background:#fafafa;">
            <td colspan="3">{{ __('dashboard.total_expenses') }}</td>
            <td>{{ number_format($totalExpenses, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@php
    // Total salaries (sum of all employees' payroll net salary)
$totalSalaries = $employees->sum(function ($emp) {
    return $emp->payroll->sum('net_salary');
});

// Total incomes
$totalIncomes = $incomes->sum('amount');

// Total expenses
$totalExpenses = $expenses->sum('amount');

    // Net profit / loss
    $netProfit = $totalIncomes - ($totalExpenses + $totalSalaries);
@endphp


<h4 class="mt-4">{{ __('dashboard.branch_financial_summary') }}</h4>

<div class="row mt-3">

    <!-- Total Income -->
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h6>{{ __('dashboard.total_income') }}</h6>
                <h4>{{ number_format($totalIncomes, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Total Expense -->
    <div class="col-md-3">
        <div class="card text-white bg-danger shadow-sm">
            <div class="card-body">
                <h6>{{ __('dashboard.total_expenses') }}</h6>
                <h4>{{ number_format($totalExpenses, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Total Salaries -->
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h6>{{ __('dashboard.total_salaries_paid') }}</h6>
                <h4>{{ number_format($totalSalaries, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Net Profit/Loss -->
    <div class="col-md-3">
        <div class="card text-white {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} shadow-sm">
            <div class="card-body">
                <h6>{{ __('dashboard.net_profit') }}</h6>
                <h4>{{ number_format($netProfit, 2) }}</h4>
            </div>
        </div>
    </div>

</div>
@php
    $companyId = $employees->first()->company_id ?? 0;
@endphp

<div class="mt-4 d-flex gap-2">
    <!-- PDF Export Button -->
    <!-- PDF Export Button -->
    <a href="{{ route('finance.branch.report.pdf', ['branch_id' => $branchId ?? 0, 'company_id' => $companyId ?? 0]) }}"
        class="btn btn-danger" target="_blank">
        <i class="fas fa-file-pdf mr-1"></i> {{ __('dashboard.print') }}
    </a>

    {{-- <a href="{{ route('finance.branch.report.pdf', ['branch_id' => $branchId ?? 0, 'company_id' => $companyId ?? 0]) }}"
        target="_blank" class="btn btn-primary">
        <i class="fas fa-print mr-1"></i> Print
    </a> --}}


</div>

<script>
    function printBranchReport() {
        // Select the entire printable report content
        var printContents = document.getElementById('printable-report').innerHTML;
        var originalContents = document.body.innerHTML;

        // Replace the body with the report content
        document.body.innerHTML = printContents;

        // Open print dialog
        window.print();

        // Restore the original page content
        document.body.innerHTML = originalContents;

        // Optional: reload scripts to make JS work again
        window.location.reload();
    }
</script>
