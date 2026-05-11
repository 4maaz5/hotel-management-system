<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'level',
        'description',
        'status',
        'start_date',
        'end_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
