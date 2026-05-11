<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDocument extends Model
{
    protected $fillable = [
        'company_partner_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function partner()
    {
        return $this->belongsTo(CompanyPartner::class);
    }
}
