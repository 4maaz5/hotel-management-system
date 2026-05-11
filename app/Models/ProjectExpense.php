<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'expense_date', 'amount', 'category', 'documents'];

    protected $casts = ['documents' => 'array'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
