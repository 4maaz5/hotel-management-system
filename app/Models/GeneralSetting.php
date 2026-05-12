<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use BelongsToTenant;

    protected $table = 'general_settings';

    protected $fillable = ['hrm_name', 'logo_path', 'email', 'phone', 'dashboard_background', 'company_id'];
}
