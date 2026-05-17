<?php

namespace App\Exports;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Models\Branch;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommissionExport implements FromCollection, WithHeadings
{
    use ScopesTenantAccess;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getFilteredData()
    {
        $query = Income::with('employee', 'branch');
        $user = $this->request->user();

        if (! $this->isSuperAdmin($user)) {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            } else {
                $query->whereIn('branch_id', Branch::where('company_id', $user->company_id)->pluck('id'));
            }
        }

        if ($this->request->employee_id) {
            $query->where('employee_id', $this->request->employee_id);
        }

        if ($this->request->branch_id) {
            if (! $this->isSuperAdmin($user) && ! $this->userCanAccessBranch((int) $this->request->branch_id, $user)) {
                $query->whereRaw('1 = 0');
            }

            $query->where('branch_id', $this->request->branch_id);
        }

        if ($this->request->month) {
            $query->whereMonth('income_date', $this->request->month);
        }

        if ($this->request->date_from && $this->request->date_to) {
            $query->whereBetween('income_date', [
                $this->request->date_from,
                $this->request->date_to,
            ]);
        }

        $records = $query->get();

        foreach ($records as $rec) {
            $percent = $rec->employee?->commission_percentage ?? 0;
            $rec->commission_earned = ($rec->amount * $percent) / 100;
        }

        return $records;
    }

    public function collection()
    {
        return $this->getFilteredData()->map(function ($rec) {
            return [
                $rec->employee ? trim($rec->employee->first_name.' '.$rec->employee->last_name) : '-',
                $rec->branch?->name ?? '-',
                'Amount' => $rec->amount,
                $rec->employee?->commission_percentage ?? 0,
                'Commission Earned' => $rec->commission_earned,
                $rec->income_date ? Carbon::parse($rec->income_date)->format('Y-m-d') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Branch',
            'Amount',
            'Commission %',
            'Commission Earned',
            'Date',
        ];
    }
}
