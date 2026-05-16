<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use BelongsToTenant;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'company_id',
        'title',
        'title_ar',
        'content',
        'content_ar',
        'keywords',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];
}
