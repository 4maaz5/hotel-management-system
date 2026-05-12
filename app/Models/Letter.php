<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'letter_type',
        'letter_number',
        'subject',
        'body',
        'hijri_date',
        'gregorian_date',
        'created_by',
        'pdf_path',
        'receiver_name',
        'letter_setting_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function letterSetting()
    {
        return $this->belongsTo(LetterSetting::class, 'letter_setting_id');
    }
}
