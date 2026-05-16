<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['warehouse_id', 'room_id', 'product_id', 'quantity'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
