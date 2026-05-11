<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_id',
        'company_id',
        'location',
        'manager',
        'email',
        'phone',
        'status',
        'total_rent',
        'market_price',
        'sale_price',
        'rent_start_date',
        'rent_end_date',
        'damage_assist',
        'installments',
    ];

    public function budget()
    {
        return $this->hasMany(Budget::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);

    }

    public function employees()
    {
        return $this->hasMany(\App\Models\Employee::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function warehouses()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class, 'branch_id', 'id');
    }
}
