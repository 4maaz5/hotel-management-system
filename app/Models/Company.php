<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    protected $fillable = [
        'name', 'legal_name', 'logo',
        'cr_number', 'cr_expiry', 'vat_number',
        'email', 'phone', 'street', 'district', 'city', 'zip_code',
        'website', 'industry_type', 'is_active',
        'start_date', 'end_date', 'subscription_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function booted()
    {
        static::deleting(function ($company) {
            // Delete company logo
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            // Delete all related documents and their files
            foreach ($company->documents as $doc) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete(); // Delete the document record
            }
            foreach ($company->brands as $brand) {
                if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                    Storage::disk('public')->delete($brand->logo);
                }
            }
        });
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function isActiveSubscription(?CarbonInterface $date = null): bool
    {
        $date ??= now()->startOfDay();

        if ($this->subscription_status !== 'active') {
            return false;
        }

        if (! $this->start_date || ! $this->end_date) {
            return false;
        }

        return $date->betweenIncluded(
            $this->start_date->copy()->startOfDay(),
            $this->end_date->copy()->endOfDay()
        );
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function partners()
    {
        return $this->hasMany(CompanyPartner::class);
    }
}
