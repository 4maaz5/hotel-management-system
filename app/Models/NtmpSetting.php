<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Support\PropertyContext;
use Illuminate\Database\Eloquent\Model;

class NtmpSetting extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'enabled',
        'mode',
        'driver',
        'provider_name',
        'endpoint',
        'api_key',
        'username',
        'password',
        'branch_reference',
        'license_reference',
        'establishment_reference',
        'connection_status',
        'last_synced_at',
        'notes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_synced_at' => 'datetime',
        'api_key' => 'encrypted',
        'password' => 'encrypted',
    ];

    public static function current(): self
    {
        $branchId = app(PropertyContext::class)->branchId();

        abort_unless($branchId, 422, 'Please select or create a branch first.');

        return static::firstOrCreate(
            ['branch_id' => $branchId],
            [
                'enabled' => false,
                'mode' => config('services.ntmp.default_mode', 'simulation'),
                'driver' => config('services.ntmp.default_driver', 'fake'),
                'connection_status' => 'not_connected',
            ]
        );
    }
}
