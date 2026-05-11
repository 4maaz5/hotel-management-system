<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Support\PropertyContext;
use Illuminate\Database\Eloquent\Model;

class ShomoosSetting extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'enabled',
        'mode',
        'driver',
        'provider_name',
        'endpoint',
        'username',
        'password',
        'branch_reference',
        'license_reference',
        'connection_status',
        'last_synced_at',
        'notes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_synced_at' => 'datetime',
        'password' => 'encrypted',
    ];

    public static function current(): self
    {
        $propertyId = app(PropertyContext::class)->id();

        abort_unless($propertyId, 422, 'Please select or create a branch first.');

        return static::firstOrCreate(
            ['property_id' => $propertyId],
            [
                'enabled' => false,
                'mode' => config('services.shomoos.default_mode', 'simulation'),
                'driver' => config('services.shomoos.default_driver', 'fake'),
                'connection_status' => 'not_connected',
            ]
        );
    }
}
