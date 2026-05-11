<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterSetting extends Model
{
    protected $fillable = [
        'company_name_ar',
        'company_logo',
        'authorized_sign_name',
        'authorized_sign_title',
        'signature_image',
        'stamp_image',
    ];
}
