<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'block_id',
        'name',
        'order',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
