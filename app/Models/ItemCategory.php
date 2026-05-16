<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'status',
        'outlet_id',
        'name',
        'ntmp_category',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(OutletSetup::class, 'outlet_id');
    }
}
