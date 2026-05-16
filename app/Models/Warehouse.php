<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['company_id', 'name', 'branch_id', 'type'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
