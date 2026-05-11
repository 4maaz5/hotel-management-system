<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskMedia extends Model
{
    use BelongsToTenant, BelongsToCurrentProperty;

    protected $table = 'task_media';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'task_id',
        'file_path',
        'file_name',
        'file_type',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    public function task()
    {
        return $this->belongsTo(HousekeepingTask::class, 'task_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
