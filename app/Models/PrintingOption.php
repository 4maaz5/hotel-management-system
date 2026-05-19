<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PrintingOption extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'report_key',
        'report_name',
        'enabled',
        'letter_head',
        'blank_paper',
        'cashier_paper',
        'contract_template_type',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'letter_head' => 'boolean',
        'blank_paper' => 'boolean',
        'cashier_paper' => 'boolean',
    ];

    public static function ensureDefaultsForTenant(int $companyId): void
    {
        $globalOptions = self::withoutGlobalScopes()
            ->whereNull('company_id')
            ->orderBy('id')
            ->get(['report_key', 'report_name']);

        foreach ($globalOptions as $option) {
            self::query()->updateOrCreate(
                [
                    'company_id' => $companyId,
                    'report_key' => $option->report_key,
                ],
                [
                    'report_name' => $option->report_name,
                ]
            );
        }
    }
}
