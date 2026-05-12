<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingOption extends Model
{
    protected $fillable = ['report_key', 'report_name', 'letter_head', 'blank_paper', 'cashier_paper'];
}
