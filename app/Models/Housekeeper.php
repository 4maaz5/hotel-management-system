<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Housekeeper extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'is_active',
        'sms_notification',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sms_notification' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEmployeeNameAttribute()
    {
        return $this->user->name ?? '';
    }

    public function getMobileNumberAttribute()
    {
        $contactInfo = $this->user->contact_info ?? [];
        if (is_string($contactInfo)) {
            $contactInfo = json_decode($contactInfo, true);
        }
        return $contactInfo['mobile_number'] ?? $contactInfo['mobile'] ?? $contactInfo['phone'] ?? '';
    }

    public function getUsernameAttribute()
    {
        return $this->user->name ?? '';
    }
}
