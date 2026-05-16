<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PropertyPhoto extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'photo_path',
        'photo_order',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }
}
