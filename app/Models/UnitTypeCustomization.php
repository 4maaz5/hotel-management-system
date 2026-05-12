<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTypeCustomization extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'unit_type_customizations';

    protected $fillable = [
        'company_id',
        'tenant_id',
        'unit_type_id',
        'name',
        'website_name_en',
        'website_name_ar',
        'unit_area',
        'single_beds',
        'double_beds',
        'base_occupancy',
        'description',
        'website_summary_en',
        'website_summary_ar',
        'website_description_en',
        'website_description_ar',
        'website_slug',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'is_published_online',
        'website_sort_order',
    ];

    protected $casts = [
        'unit_area' => 'decimal:2',
        'is_published_online' => 'boolean',
    ];

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function rate()
    {
        return $this->hasOne(UnitTypeRate::class, 'unit_type_id', 'unit_type_id');
    }

    public function images()
    {
        return $this->hasMany(UnitTypeCustomizationImage::class, 'type_customization_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(UnitTypeCustomizationImage::class, 'type_customization_id')
            ->where('is_primary', true);
    }
}
