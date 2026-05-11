<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenterCategory extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [ 'name'];

    public function costCenters()
    {
        return $this->hasMany(CostCenter::class, 'category_id');
    }
}
