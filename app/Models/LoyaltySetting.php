<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'criteria', 'threshold_value', 'upgrade_to_class_id', 'is_active', 'created_by'];

    public function guestClass()
    {
        return $this->belongsTo(GuestClass::class, 'upgrade_to_class_id');
    }
}
