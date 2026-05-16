<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChFavorite extends Model
{
    use BelongsToTenant, UUID;
}
