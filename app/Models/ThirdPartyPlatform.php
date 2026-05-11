<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
    ];

    /**
     * Relationship: Third Party Platform belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
