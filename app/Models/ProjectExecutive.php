<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExecutive extends Model
{
    protected $fillable = [
        'project_id',
        'responsible_person_name',
        'contract_reference',
        'company_name',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
