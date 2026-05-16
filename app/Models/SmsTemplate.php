<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = ['company_id', 'type', 'recipient', 'enabled', 'message'];
}
