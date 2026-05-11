<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OutletItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'status',
        'name',
        'type',
        'outlet_id',
        'category_id',
        'description',
        'price',
        'no_tax',
        'no_price',
        'price_is_user_defined',
    ];

    protected $casts = [
        'status' => 'boolean',
        'no_tax' => 'boolean',
        'no_price' => 'boolean',
        'price_is_user_defined' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function outlet()
    {
        return $this->belongsTo(OutletSetup::class);
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class);
    }
}
