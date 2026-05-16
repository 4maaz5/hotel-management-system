<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'name',
        'category',
        'penalty_type',
        'value',
        'tax_applicable',
        'is_active',
        'description',
    ];
}
