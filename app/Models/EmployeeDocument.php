<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'file_path',
        'issue_date',
        'document_number',
        'expiration_date',
        'image',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
