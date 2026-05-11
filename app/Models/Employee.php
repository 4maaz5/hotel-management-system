<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'employee_id', 'email', 'phone',
        'designation', 'branch_id', 'join_date',
        'residence_expiry_date', 'bank_name', 'account_number', 'image', 'user_id',
        'company_id', 'brand_id', 'department_id', 'base_salary', 'qr_code', 'commission_percentage', 'commission_type',
        'shift_id', 'overtime',
    ];

    protected static function booted()
    {
        static::deleting(function ($employee) {
            // 1. Delete employee image
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }

            // 2. Delete insurance documents
            foreach ($employee->insurances as $insurance) {
                if ($insurance->document) {
                    Storage::disk('public')->delete($insurance->document);
                }
            }

            // 3. Delete employee documents
            foreach ($employee->documents as $document) {
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }
            }
            foreach ($employee->documents as $document) {
                if ($document->image) {
                    Storage::disk('public')->delete($document->image);
                }
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
