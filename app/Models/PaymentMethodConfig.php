<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodConfig extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'payment_method_id',
        'description',
        'is_active',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->paymentMethod?->name;
    }

    public static function idsForMethodNameLike(string $pattern): array
    {
        return static::query()
            ->whereHas('paymentMethod', function ($query) use ($pattern) {
                $query->where('name', 'like', $pattern);
            })
            ->pluck('id')
            ->all();
    }

    public static function idsForMethodNames(array $names): array
    {
        return static::query()
            ->whereHas('paymentMethod', function ($query) use ($names) {
                $query->whereIn('name', $names);
            })
            ->pluck('id')
            ->all();
    }
}
