<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $table = 'general_settings';

    protected $fillable = ['hrm_name', 'logo_path', 'email', 'phone', 'dashboard_background'];
}
