<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'stock_request_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }
}
