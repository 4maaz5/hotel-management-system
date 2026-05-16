<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'credit_note_number',
        'invoice_type',
        'reservation_id',
        'outlet_id',
        'invoice_id',
        'invoice_number',
        'guest_id',
        'cn_date',
        'period_from',
        'period_to',
        'amount',
        'qr_code',
        'created_by',
    ];

    protected $casts = [
        'cn_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'amount' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }

    public static function generateCreditNoteNumber()
    {
        $lastNote = self::withoutGlobalScope(StaffCurrentPropertyScope::class)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastNote ? $lastNote->id + 1 : 1;

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
