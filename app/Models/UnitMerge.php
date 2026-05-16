<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class UnitMerge extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'block_id',
        'floor_id',
        'unit_class_id',
        'merge_code',
        'unit_a_id',
        'unit_b_id',
        'is_active',
    ];

    public function unitA()
    {
        return $this->belongsTo(Unit::class, 'unit_a_id');
    }

    public function unitB()
    {
        return $this->belongsTo(Unit::class, 'unit_b_id');
    }
}
