<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['company_id', 'property_name', 'default_language', 'show_property_name'];
}
