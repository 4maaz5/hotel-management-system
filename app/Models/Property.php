<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\PropertyContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'uuid',
        'status',
        'property_name_en',
        'property_name_ar',
        'report_name_en',
        'report_name_ar',
        'property_code',
        'property_type_id',
        'owner_user_id',
        'logo_url',
        'account_version',
        'account_expiry_date',
        'country_id',
        'region_id',
        'city_id',
        'district_id',
        'address_en',
        'address_ar',
        'building_no',
        'secondary_no',
        'postal_code',
        'po_box',
        'latitude',
        'longitude',
        'time_zone',
        'phone',
        'mobile',
        'email',
        'website',
        'active_units_count',
        'max_units_count',
        'fax_number',
        'hot_line',
        'admin_number',
    ];

    protected $casts = [
        'account_expiry_date' => 'date',
        'active_units_count' => 'integer',
        'max_units_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $property): void {
            if (empty($property->uuid)) {
                $property->uuid = (string) Str::uuid();
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'property_user')
            ->withTimestamps();
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'branch_id', 'branch_id');
    }

    public function floors()
    {
        return $this->hasMany(Floor::class, 'branch_id', 'branch_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'branch_id', 'branch_id');
    }

    public function tourismLicense()
    {
        return $this->hasOne(PropertyTourismLicense::class, 'branch_id', 'branch_id');
    }

    public function commercialDetail()
    {
        return $this->hasOne(PropertyCommercialDetail::class, 'branch_id', 'branch_id');
    }

    public function additionalDetail()
    {
        return $this->hasOne(PropertyAdditionalDetail::class, 'branch_id', 'branch_id');
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class, 'branch_id', 'branch_id');
    }

    public function mainPhoto()
    {
        return $this->hasOne(PropertyPhoto::class, 'branch_id', 'branch_id')->where('is_main', true);
    }

    public static function current(array $relations = []): ?self
    {
        $query = static::query();

        if ($relations !== []) {
            $query->with($relations);
        }

        $propertyId = app(PropertyContext::class)->id();

        if ($propertyId) {
            return $query->where('id', $propertyId)->first();
        }

        return $query->first();
    }
}
