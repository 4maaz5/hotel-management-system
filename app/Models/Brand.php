<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'company_id',
        'name',
        'logo',
    ];

    protected static function booted()
    {
        static::deleting(function ($brand) {
            // Delete logo file if it exists
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
