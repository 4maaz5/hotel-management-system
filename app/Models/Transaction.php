<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['type', 'branch_id', 'amount', 'date', 'description', 'created_by'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
