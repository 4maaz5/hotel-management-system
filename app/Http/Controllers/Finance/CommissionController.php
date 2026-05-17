<?php

namespace App\Http\Controllers\Finance;

use App\Exports\CommissionExport;
use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Income;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CommissionController extends Controller
{
    use ScopesTenantAccess;

    // public function commissionReport(Request $request)
    // {
    //     $user = $request->user();

    //     // Base queries
    //     $employees = Employee::query();
    //     $branches = Branch::query();
    //     $query = Income::with('employee', 'branch');

    //     // Role-based restrictions
    //     if ($user->hasRole('manager')) {
    //         // Manager sees only their branch
    //         $query->where('branch_id', $user->branch_id);
    //         $employees->where('branch_id', $user->branch_id);
    //         $branches->where('id', $user->branch_id);
    //     }

    //     // Filters from request
    //     $employeeId = $request->employee_id;
    //     $branchId = $request->branch_id;
    //     $month = $request->month;
    //     $dateFrom = $request->date_from;
    //     $dateTo = $request->date_to;

    //     if ($employeeId) {
    //         $query->where('employee_id', $employeeId);
    //     }

    //     if ($branchId) {
    //         $query->where('branch_id', $branchId);
    //     }

    //     if ($month) {
    //         $query->whereMonth('income_date', $month);
    //     }

    //     if ($dateFrom && $dateTo) {
    //         $query->whereBetween('income_date', [$dateFrom, $dateTo]);
    //     }

    //     $records = $query->get();

    //     // Calculate commission for each row
    //     foreach ($records as $rec) {
    //         $commissionPercent = $rec->employee->commission_percent ?? 0;
    //         $rec->commission_earned = ($rec->amount * $commissionPercent) / 100;
    //     }

    //     // Get employees and branches for filters
    //     $employees = $employees->get();
    //     $branches = $branches->get();

    //     return view('Admin.Backend.Commission.index', compact(
    //         'records', 'employees', 'branches'
    //     ));
    // }

    public function commissionReport(Request $request)
    {
        $user = $request->user();

        $employees = Employee::query();
        $branches = Branch::query();

        if (! $user->hasRole('super_admin')) {
            if ($user->branch_id) {
                $employees->where('branch_id', $user->branch_id);
                $branches->where('id', $user->branch_id);
            } else {
                $companyBranchIds = Branch::where('company_id', $user->company_id)->pluck('id');
                $employees->whereIn('branch_id', $companyBranchIds);
                $branches->whereIn('id', $companyBranchIds);
            }
        }

        $records = (new CommissionExport($request))->getFilteredData();

        // Get employees and branches for filters
        $employees = $employees->get();
        $branches = $branches->get();

        return view('Admin.Backend.Commission.index', compact(
            'records', 'employees', 'branches'
        ));
    }

    // EXPORT TO EXCEL
    public function exportCommissionExcel(Request $request)
    {
        return Excel::download(new CommissionExport($request), 'commission_report.xlsx');
    }

    // EXPORT TO PDF
    public function exportCommissionPDF(Request $request)
    {
        $data = (new CommissionExport($request))->getFilteredData();
        $pdf = Pdf::loadView('Admin.Backend.Finance.commission_pdf', ['records' => $data]);

        return $pdf->download('commission_report.pdf');
    }
}
