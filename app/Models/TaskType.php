<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use BelongsToTenant;

    protected $table = 'housekeeping_task_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
        'is_routine',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_routine' => 'boolean',
    ];
}
