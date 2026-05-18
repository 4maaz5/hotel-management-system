<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id', 'branch_id', 'name', 'location', 'project_manager', 'value', 'documents', 'timeline_type', 'start_date', 'end_date',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function executives()
    {
        return $this->hasMany(ProjectExecutive::class);
    }

    public function trackers()
    {
        return $this->hasMany(ProjectTracker::class);
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }
}
