<?php

use App\Models\DateTimeSetting;
use App\Models\Tenant;
use App\Support\TenantContext;

function currentTenant(): ?Tenant
{
    $id = app(TenantContext::class)->id();
    if (! $id) return null;
    return Tenant::with('plan')->find($id);
}

function system_settings()
{
    return cache()->rememberForever('system_settings', function () {
        return DateTimeSetting::first();
    });
}

function system_date_format()
{
    return optional(system_settings())->date_format ?? 'd-m-Y';
}

function system_time_format()
{
    return optional(system_settings())->time_format == 24 ? 'H:i' : 'h:i A';
}

function system_timezone()
{
    return optional(system_settings())->timezone ?? 'Asia/Riyadh';
}
