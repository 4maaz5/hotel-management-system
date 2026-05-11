<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdministrativeExpense extends Model
{
    protected $fillable = [
        'item_name',
        'invoice_number',
        'quantity',
        'file',
        'branch_id',
        'amount',
        'expense_date',
        'description',
        'created_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($expense) {
            if ($expense->file) {
                Storage::disk('public')->delete('administrative_expenses/'.$expense->file);
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
