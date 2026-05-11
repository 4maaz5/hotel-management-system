<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'branch_id',
        'total_budget',
        'used_budget',
        'remaining_budget',
        'status',
        'start_date',
        'end_date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
