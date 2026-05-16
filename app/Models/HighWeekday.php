<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HighWeekday extends Model
{
    use BelongsToTenant;

    protected $fillable = ['company_id', 'day_name'];
}
