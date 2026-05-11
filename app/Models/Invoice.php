<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'property_id',
        'reservation_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'discount',
        'discount_amount',
        'tax_amount',
        'security_deposit',
        'total',
        'paid_amount',
        'balance',
        'status',
        'payment_method',
        'notes',
        'qr_code',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function generateInvoiceNumber()
    {
        $lastInvoice = self::withoutGlobalScope(StaffCurrentPropertyScope::class)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastInvoice ? $lastInvoice->id + 1 : 1;

        return 'INV-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function generateQrCode()
    {
        $property = $this->reservation?->property ?: Property::current(['commercialDetail']);

        $sellerName = $property->property_name_en ?? 'Property Name';
        $vatNumber = $property->commercialDetail->vat_number ?? '';
        $timestamp = now()->toIso8601String();
        $total = number_format($this->total, 2, '.', '');
        $vatAmount = number_format($this->tax_amount, 2, '.', '');

        $fields = [
            1 => $sellerName,
            2 => $vatNumber,
            3 => $timestamp,
            4 => $total,
            5 => $vatAmount,
        ];

        $tlv = '';
        foreach ($fields as $tag => $value) {
            $tlv .= chr($tag) . chr(strlen($value)) . $value;
        }

        return base64_encode($tlv);
    }
}
