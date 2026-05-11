<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CostCenter extends Model
{
        use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'category_id',
        'description',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(CostCenterCategory::class, 'category_id');
    }
}
