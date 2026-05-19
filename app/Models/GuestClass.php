<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class GuestClass extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'blacklist',
        'class_name',
        'order_no',
        'icon',
        'discount_method',
        'discount_amount',
        'description',
    ];

    protected $casts = [
        'blacklist' => 'boolean',
        'class_name' => 'array',
        'description' => 'array',
        'discount_amount' => 'decimal:2',
    ];

    public function getClassNameAttribute($value): string
    {
        return $this->resolveTranslatedValue($value);
    }

    public function getDescriptionAttribute($value): string
    {
        return $this->resolveTranslatedValue($value);
    }

    protected function resolveTranslatedValue($value): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }

        if (is_array($value)) {
            $locale = app()->getLocale();
            $fallbackKeys = array_values(array_unique([
                $locale,
                str_contains($locale, '_') ? strtolower((string) strtok($locale, '_')) : $locale,
                'en',
                'ar',
            ]));

            foreach ($fallbackKeys as $key) {
                if (! empty($value[$key]) && is_scalar($value[$key])) {
                    return (string) $value[$key];
                }
            }

            foreach ($value as $translated) {
                if (! empty($translated) && is_scalar($translated)) {
                    return (string) $translated;
                }
            }

            return '';
        }

        return is_scalar($value) ? (string) $value : '';
    }
}
