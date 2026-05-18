<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LetterSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'company_name_ar',
        'company_logo',
        'authorized_sign_name',
        'authorized_sign_title',
        'signature_image',
        'stamp_image',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
