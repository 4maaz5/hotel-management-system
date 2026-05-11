<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Housekeeper;
use App\Models\Property;
use App\Models\StaffAttendance;
use App\Support\PropertyContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'in:checked_in,checked_out,adjusted'],
        ]);

        $query = StaffAttendance::with('property')
            ->where('user_id', $request->user()->id)
            ->when($validated['from'] ?? null, fn ($q, $date) => $q->whereDate('attendance_date', '>=', $date))
            ->when($validated['to'] ?? null, fn ($q, $date) => $q->whereDate('attendance_date', '<=', $date))
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_at');

        return response()->json(
            $query->paginate($request->integer('per_page', 20))
                ->through(fn (StaffAttendance $attendance) => $this->attendancePayload($attendance))
        );
    }

    public function show(Request $request, StaffAttendance $attendance)
    {
        abort_unless((int) $attendance->user_id === (int) $request->user()->id, 404);

        return response()->json([
            'attendance' => $this->attendancePayload($attendance->load('property')),
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $this->validateLocation($request);
        $user = $request->user();
        $property = $this->currentProperty();
        $housekeeper = $this->currentHousekeeper($user->id, $property->id);
        $now = $this->nowForProperty($property);

        $openAttendance = StaffAttendance::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->whereNull('check_out_at')
            ->where('status', 'checked_in')
            ->latest('check_in_at')
            ->first();

        if ($openAttendance) {
            throw ValidationException::withMessages([
                'attendance' => ['You already have an open attendance check-in.'],
            ]);
        }

        $geo = $this->geoPayload($property, $validated['latitude'], $validated['longitude']);
        $this->ensureGeofenceAllowed($geo);

        $attendance = StaffAttendance::create([
            'tenant_id' => $user->tenant_id,
            'property_id' => $property->id,
            'user_id' => $user->id,
            'housekeeper_id' => $housekeeper->id,
            'attendance_date' => $now->toDateString(),
            'check_in_at' => $now,
            'check_in_latitude' => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'check_in_accuracy_meters' => $validated['accuracy_meters'] ?? null,
            'check_in_distance_meters' => $geo['distance_meters'],
            'check_in_within_geofence' => $geo['within_geofence'],
            'status' => 'checked_in',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'Checked in successfully.',
            'attendance' => $this->attendancePayload($attendance),
        ], 201);
    }

    public function checkOut(Request $request)
    {
        $validated = $this->validateLocation($request);
        $user = $request->user();
        $property = $this->currentProperty();
        $now = $this->nowForProperty($property);

        $attendance = StaffAttendance::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->whereNull('check_out_at')
            ->where('status', 'checked_in')
            ->latest('check_in_at')
            ->first();

        if (! $attendance) {
            throw ValidationException::withMessages([
                'attendance' => ['No open attendance check-in was found.'],
            ]);
        }

        $geo = $this->geoPayload($property, $validated['latitude'], $validated['longitude']);
        $this->ensureGeofenceAllowed($geo);

        $attendance->update([
            'check_out_at' => $now,
            'check_out_latitude' => $validated['latitude'],
            'check_out_longitude' => $validated['longitude'],
            'check_out_accuracy_meters' => $validated['accuracy_meters'] ?? null,
            'check_out_distance_meters' => $geo['distance_meters'],
            'check_out_within_geofence' => $geo['within_geofence'],
            'status' => 'checked_out',
            'notes' => $validated['notes'] ?? $attendance->notes,
        ]);

        return response()->json([
            'message' => 'Checked out successfully.',
            'attendance' => $this->attendancePayload($attendance->fresh()),
        ]);
    }

    protected function validateLocation(Request $request): array
    {
        return $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    protected function currentProperty(): Property
    {
        $property = Property::withoutGlobalScopes()->find(app(PropertyContext::class)->id());

        if (! $property) {
            abort(422, 'No property context is available.');
        }

        return $property;
    }

    protected function currentHousekeeper(int $userId, int $propertyId): Housekeeper
    {
        $housekeeper = Housekeeper::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->where('is_active', true)
            ->first();

        if (! $housekeeper) {
            abort(403, 'This user is not an active housekeeper for the selected property.');
        }

        return $housekeeper;
    }

    protected function nowForProperty(Property $property): Carbon
    {
        $timezone = $property->time_zone ?: config('app.timezone');

        if (! in_array($timezone, timezone_identifiers_list(), true)) {
            $timezone = config('app.timezone', 'UTC');
        }

        return Carbon::now($timezone);
    }

    protected function geoPayload(Property $property, float $latitude, float $longitude): array
    {
        if ($property->latitude === null || $property->longitude === null) {
            return [
                'distance_meters' => null,
                'within_geofence' => null,
            ];
        }

        $distance = $this->distanceMeters(
            (float) $property->latitude,
            (float) $property->longitude,
            $latitude,
            $longitude
        );

        $radius = (int) config('mobile_attendance.geofence_radius_meters', 200);

        return [
            'distance_meters' => (int) round($distance),
            'within_geofence' => $distance <= $radius,
        ];
    }

    protected function ensureGeofenceAllowed(array $geo): void
    {
        if (
            config('mobile_attendance.require_geofence_match', false)
            && $geo['within_geofence'] === false
        ) {
            throw ValidationException::withMessages([
                'location' => ['You are outside the allowed attendance geofence.'],
            ]);
        }
    }

    protected function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    protected function attendancePayload(StaffAttendance $attendance): array
    {
        return [
            'id' => $attendance->id,
            'property_id' => $attendance->property_id,
            'attendance_date' => optional($attendance->attendance_date)->toDateString(),
            'status' => $attendance->status,
            'check_in_at' => optional($attendance->check_in_at)->toISOString(),
            'check_in_location' => [
                'latitude' => $attendance->check_in_latitude,
                'longitude' => $attendance->check_in_longitude,
                'accuracy_meters' => $attendance->check_in_accuracy_meters,
                'distance_meters' => $attendance->check_in_distance_meters,
                'within_geofence' => $attendance->check_in_within_geofence,
            ],
            'check_out_at' => optional($attendance->check_out_at)->toISOString(),
            'check_out_location' => [
                'latitude' => $attendance->check_out_latitude,
                'longitude' => $attendance->check_out_longitude,
                'accuracy_meters' => $attendance->check_out_accuracy_meters,
                'distance_meters' => $attendance->check_out_distance_meters,
                'within_geofence' => $attendance->check_out_within_geofence,
            ],
            'notes' => $attendance->notes,
        ];
    }
}
