<?php

return [
    'token_ttl_days' => env('MOBILE_API_TOKEN_TTL_DAYS', 30),
    'geofence_radius_meters' => env('MOBILE_ATTENDANCE_GEOFENCE_RADIUS_METERS', 200),
    'require_geofence_match' => env('MOBILE_ATTENDANCE_REQUIRE_GEOFENCE_MATCH', false),
];
