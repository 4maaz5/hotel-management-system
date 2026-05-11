<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'is_active',
    ];

    public function penalties()
    {
        return $this->belongsToMany(Penalty::class, 'cancel_reason_penalty')
            ->withPivot('auto_apply')
            ->withTimestamps();
    }
}
