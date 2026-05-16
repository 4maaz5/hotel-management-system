<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SmsUserSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['company_id', 'user_id', 'sms_template_id', 'enabled'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(SmsTemplate::class, 'sms_template_id');
    }
}
