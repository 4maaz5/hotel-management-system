<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PrintingOption extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'report_key', 'report_name', 'letter_head', 'blank_paper', 'cashier_paper'];
}
