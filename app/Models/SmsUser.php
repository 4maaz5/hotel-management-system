<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SmsUser extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function smsTypes()
    {
        return $this->hasMany(SmsUserSetting::class, 'user_id', 'user_id');
    }
}
