<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
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
