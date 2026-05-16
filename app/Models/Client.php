<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'company_name',
        'client_name',
        'cr_number',
        'vat_number',
        'email',
        'phone',
        'person_name',
        'contact',
        'address',
    ];

    public function documents()
    {
        return $this->hasMany(ClientDocument::class);
    }
}
