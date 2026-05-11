<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'sku',
        'unit',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
