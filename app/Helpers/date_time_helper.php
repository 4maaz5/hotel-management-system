<?php

use App\Models\DateTimeSetting;

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
