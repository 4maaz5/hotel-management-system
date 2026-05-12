<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Income;
use App\Models\Payroll;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     // Super Admin → see all
    //     if ($user->hasRole('super_admin')) {
    //         $employees = Employee::with('branch')->get();
    //         $payrolls = Payroll::with('employee')->latest()->get();
    //         $payrollCards = Payroll::with('employee')->latest()->paginate(10);
    //     }

    //     // Manager → see only their branch employees and payrolls
    //     elseif ($user->hasRole('manager')) {
    //         $employees = Employee::with('branch')
    //             ->where('branch_id', $user->branch_id)
    //             ->get();

    //         $payrolls = Payroll::with('employee')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->get();
    //         $payrollCards = Payroll::with('employee')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->paginate(10);
    //     }

    //     // Employee → see only their own payroll
    //     elseif ($user->hasRole('employee')) {
    //         $employee = Employee::where('user_id', $user->id)->first();

    //         $employees = collect([$employee]); // single employee in a collection

    //         $payrolls = Payroll::with('employee')
    //             ->where('employee_id', $employee->id)
    //             ->latest()
    //             ->get();
    //     }

    //     return view('Admin.Backend.GeneratePayslip.payslip', compact('employees', 'payrolls', 'payrollCards'));
    // }

    public function index()
    {
        $user = Auth::user();

        // Initialize variables
        $employees = collect();
        $payrolls = collect();
        $payrollCards = collect();

        if ($user->hasRole('super_admin')) {
            $employees = Employee::with('branch')->get();
            $payrolls = Payroll::with('employee')->latest()->get();
            $payrollCards = Payroll::with('employee')->latest()->paginate(10);
        } elseif ($user->branch_id) {
            $employees = Employee::with('branch')
                ->where('branch_id', $user->branch_id)
                ->get();
            $payrolls = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->latest()->get();
            $payrollCards = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->latest()->paginate(10);
        } else {
            $employees = Employee::where('company_id', $user->company_id)->get();
            $payrolls = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->latest()->get();
            $payrollCards = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->latest()->paginate(10);
        }

        return view('Admin.Backend.GeneratePayslip.payslip', compact(
            'employees',
            'payrolls',
            'payrollCards'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'month' => 'required',
            'commission' => 'required',
            'total_amount' => 'required|numeric',
            'deductions' => 'required',
            'basic_salary' => 'required|numeric',
            'allowance' => 'nullable|numeric',
            'net_pay' => 'required|numeric',
            'status' => 'required|in:Pending,Paid,Cancelled',
        ]);
        //  Check if payroll already exists for this employee + month
        $exists = Payroll::where('employee_id', $request->employee_id)
            ->where('month', $request->month)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.payroll_exists_for_month'),
            ], 422);
        }

        $payroll = Payroll::create($validated);
        if ($payroll->status === 'Paid') {
            Transaction::create([
                'type' => 'payroll',
                'amount' => $payroll->total_amount ?? 0,
                'date' => now(),
                'description' => 'Salary payment for '.\Carbon\Carbon::parse($payroll->month)->format('F Y'),
                'branch_id' => $payroll->employee->branch_id,
                'created_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.payroll_created_successfully'),
            'data' => $payroll->load('employee'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'month' => 'required',
            'basic_salary' => 'required|numeric',
            // 'total_amount' => 'required|numeric',
            'allowance' => 'nullable|numeric',
            'net_pay' => 'required|numeric',
            'status' => 'required|in:Pending,Paid,Cancelled',
        ]);

        $payroll = Payroll::findOrFail($id);

        // Store the old status before update
        $oldStatus = $payroll->status;

        // Update the payroll
        $payroll->update($validated);

        // If status changed from not-paid to paid → create transaction
        if ($oldStatus !== 'Paid' && $payroll->status === 'Paid') {
            Transaction::create([
                'type' => 'Payroll',
                'amount' => $payroll->total_amount,
                'date' => now(),
                'description' => 'Salary payment for '.\Carbon\Carbon::parse($payroll->month)->format('F Y'),
                'branch_id' => $payroll->employee->branch_id,
                'created_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.payroll_updated_successfully'),
            'data' => $payroll->load('employee'),
        ]);
    }

    public function destroy($id)
    {
        Payroll::findOrFail($id)->delete();

        return response()->json(['status' => 'success', 'message' => __('messages.payroll_deleted_successfully')]);
    }

    public function create()
    {
        return view('Admin.Backend.GeneratePayslip.create');
    }

    public function edit()
    {
        return view('Admin.Backend.GeneratePayslip.edit');
    }

    public function getEmployeePayrollData($employeeId, $month)
    {
        $employee = Employee::findOrFail($employeeId);

        // Parse month into start and end date
        $startDate = $month.'-01';
        $endDate = date('Y-m-t', strtotime($startDate)); // last day of month

        // Sum commissions from incomes
        $commission = Income::where('employee_id', $employeeId)
            ->whereBetween('income_date', [$startDate, $endDate])
            ->sum(DB::raw('amount * ('.($employee->commission_percentage ?? 0).' / 100)'));

        $basicSalary = $employee->base_salary ?? 0;
        $allowance = $employee->allowance ?? 0;
        $netPay = $basicSalary + $allowance + $commission;

        return response()->json([
            'basic_salary' => $basicSalary,
            'allowance' => $allowance,
            'commission' => $commission,
            'net_pay' => $netPay,
        ]);
    }
}
