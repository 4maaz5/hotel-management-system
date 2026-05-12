<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeExpense;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Income;
use App\Models\Payroll;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Default: total values
        $totalExpenses = 0;
        $payrollCost = 0;
        $pendingTransactions = 0;
        $paidTransactions = 0;
        $pendingPayroll = 0;
        $totalIncome = 0;
        $monthlySalary = 0;
        $payrollPercentage = 0;

        if ($user->hasRole('super_admin')) {
            // Super admin sees everything
            $totalExpenses = AdministrativeExpense::sum('amount');
            $payrollCost = Payroll::sum('net_pay');
            $pendingTransactions = Payroll::where('status', 'pending')->count();
            $paidTransactions = Payroll::where('status', 'paid')->count();
            $pendingPayroll = Payroll::where('status', 'pending')->sum('net_pay');
            $totalIncome = Income::sum('amount');

            $monthlySalary = Payroll::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('net_pay');

            $totalPayroll = Payroll::sum('net_pay');
            $payrollPercentage = $totalPayroll > 0
                ? round(($monthlySalary / $totalPayroll) * 100, 2)
                : 0;

            $branches = Branch::all();
            $transactions = Transaction::all();
            $transactionCards = Transaction::paginate(10);
        } elseif ($user->branch_id) {
            // User has a branch → scope by that branch
            $branchId = $user->branch_id;

            $totalExpenses = AdministrativeExpense::where('branch_id', $branchId)->sum('amount');
            $payrollCost = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->sum('net_pay');

            $pendingTransactions = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->where('status', 'pending')->count();

            $paidTransactions = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->where('status', 'paid')->count();

            $pendingPayroll = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->where('status', 'pending')->sum('net_pay');

            $totalIncome = Income::where('branch_id', $branchId)->sum('amount');

            $monthlySalary = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('net_pay');

            $totalPayroll = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $branchId))
                ->sum('net_pay');

            $payrollPercentage = $totalPayroll > 0
                ? round(($monthlySalary / $totalPayroll) * 100, 2)
                : 0;

            $branches = Branch::where('id', $branchId)->get();
            $transactions = Transaction::where('branch_id', $branchId)->get();
            $transactionCards = Transaction::where('branch_id', $branchId)->paginate(10);
        } else {
            // Owner (no branch_id): TenantScope auto-scopes BelongsToTenant models
            // Non-tenant models (AdministrativeExpense, Income, Payroll via Employee) without branch filter
            $totalExpenses = AdministrativeExpense::sum('amount');
            $payrollCost = Payroll::whereHas('employee', fn ($q) => $q)->sum('net_pay');

            $pendingTransactions = Payroll::whereHas('employee', fn ($q) => $q)
                ->where('status', 'pending')->count();

            $paidTransactions = Payroll::whereHas('employee', fn ($q) => $q)
                ->where('status', 'paid')->count();

            $pendingPayroll = Payroll::whereHas('employee', fn ($q) => $q)
                ->where('status', 'pending')->sum('net_pay');

            $totalIncome = Income::sum('amount');

            $monthlySalary = Payroll::whereHas('employee', fn ($q) => $q)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('net_pay');

            $totalPayroll = Payroll::whereHas('employee', fn ($q) => $q)
                ->sum('net_pay');

            $payrollPercentage = $totalPayroll > 0
                ? round(($monthlySalary / $totalPayroll) * 100, 2)
                : 0;

            $branches = Branch::all();
            $transactions = Transaction::all();
            $transactionCards = Transaction::paginate(10);
        }

        return view('Admin.Backend.Finance.dashboard', compact(
            'totalExpenses',
            'payrollCost',
            'pendingTransactions',
            'paidTransactions',
            'pendingPayroll',
            'totalIncome',
            'monthlySalary',
            'payrollPercentage',
            'branches',
            'transactions',
            'transactionCards'
        ));
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $selectedCompany = $request->company_id ?? null;

        $branches = collect(); // default empty
        $brands = collect();   // default empty

        if ($selectedCompany) {
            $company = Company::with(['branches', 'brands'])->find($selectedCompany);

            if ($company) {
                $branches = $company->branches;
                $brands = $company->brands;
            }
        }

        $user = Auth::user();

        // Initialize Variables
        $totalIncome = 0;
        $totalExpenses = 0;
        $payrollCost = 0;
        $pendingTransactions = 0;

        if ($user->hasRole('super_admin')) {

            $totalIncome = Income::sum('amount');
            $totalExpenses = AdministrativeExpense::sum('amount');

            $payrollCost = Payroll::sum('net_pay');

            $pendingTransactions = Payroll::where('status', 'pending')->count();

        } elseif ($user->hasRole('manager') && $user->branch_id) {

            $branchId = $user->branch_id;

            $totalIncome = Income::where('branch_id', $branchId)->sum('amount');
            $totalExpenses = AdministrativeExpense::where('branch_id', $branchId)->sum('amount');

            $payrollCost = Payroll::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->sum('net_pay');

            $pendingTransactions = Payroll::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->where('status', 'pending')->count();

        } else {
            // Owner (no branch_id): show all company data
            $totalIncome = Income::sum('amount');
            $totalExpenses = AdministrativeExpense::sum('amount');
            $payrollCost = Payroll::whereHas('employee', function ($q) {
                // TenantScope auto-scopes Employee by company_id
            })->sum('net_pay');
            $pendingTransactions = Payroll::whereHas('employee', function ($q) {
                // TenantScope auto-scopes Employee by company_id
            })->where('status', 'pending')->count();
        }

        return view('Admin.Backend.Finance.report', compact(
            'companies',
            'selectedCompany',
            'branches',
            'brands',
            'totalIncome',
            'totalExpenses',
            'payrollCost',
            'pendingTransactions'
        ));
    }

    public function branchFinanceData(Request $request)
    {
        $branchId = $request->branch_id;
        $brandId = $request->brand_id ?? null;
        $month = \Carbon\Carbon::parse($request->month)->format('Y-m');

        // Parse the selected month from request
        $selectedMonth = $month ?? now()->format('Y-m'); // default to current month
        [$year, $month] = explode('-', $selectedMonth);

        $employees = Employee::with(['payroll' => function ($q) use ($selectedMonth) {
            $q->where('month', $selectedMonth); // exact match '2025-12'
        }])
            ->where('branch_id', $branchId)
            ->get();

        $incomes = Income::where('branch_id', $branchId)
            ->whereMonth('income_date', $month)
            ->whereYear('income_date', $year)
            ->get();

        $expenses = AdministrativeExpense::where('branch_id', $branchId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->get();

        return view('Admin.Backend.partials.branch-report', compact('employees', 'incomes', 'expenses', 'branchId'));
    }

    public function branchReportPdf(Request $request)
    {
        $branchId = $request->branch_id;
        $companyId = $request->company_id;
        $company = Company::find($companyId);

        $employees = Employee::with('payroll')
            ->where('branch_id', $branchId)
            ->get();

        $incomes = Income::where('branch_id', $branchId)->get();
        $expenses = AdministrativeExpense::where('branch_id', $branchId)->get();

        // Return the view for printing
        return view('Admin.Backend.Finance.branch-report-pdf', compact('employees', 'incomes', 'expenses', 'company'));
    }
}
