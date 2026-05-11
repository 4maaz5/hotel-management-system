<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class CommissionExport implements FromCollection
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getFilteredData()
    {
        $query = Income::with('employee', 'branch');

        if ($this->request->employee_id) {
            $query->where('employee_id', $this->request->employee_id);
        }

        if ($this->request->branch_id) {
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
            $percent = $rec->employee->commission_percent ?? 0;
            $rec->commission_earned = ($rec->amount * $percent) / 100;
        }

        return $records;
    }

    public function collection()
    {
        return $this->getFilteredData()->map(function ($rec) {
            return [
                'Employee' => $rec->employee->first_name.' '.$rec->employee->last_name,
                'Branch' => $rec->branch->name,
                'Amount' => $rec->amount,
                'Commission %' => $rec->employee->commission_percent,
                'Commission Earned' => $rec->commission_earned,
                'Date' => $rec->income_date,
            ];
        });
    }
}
