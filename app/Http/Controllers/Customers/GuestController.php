<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->visibleGuests($request);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('created_at', 'desc')->paginate(20);
        $guestClasses = GuestClass::where('is_active', 1)->get();

        return view('admin.guest.index', compact('guests', 'guestClasses'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'second_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'guest_class_id' => 'nullable|exists:guest_classes,id',
            'nationality' => 'nullable|string|max:100',
            'nationality_code' => 'nullable|string|size:3|regex:/^[A-Za-z]{3}$/',
            'guest_type' => 'nullable|in:individual,family,corporate',
            'id_type' => 'nullable|in:national_id,passport,iqama,driver_license',
            'id_number' => 'nullable|string|max:50',
            'id_issue_country' => 'nullable|string|max:10',
            'id_expiry_date' => 'nullable|date',
            'visa_number' => 'nullable|string|max:50',
            'arrival_from' => 'nullable|string|max:100',
            'id_serial' => 'nullable|in:first,second,third,last',
            'mobile_dial_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'work_place' => 'nullable|string|max:200',
            'work_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'car_license_plate' => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (! empty($validated['nationality_code'])) {
            $validated['nationality_code'] = strtoupper($validated['nationality_code']);
        }

        if (! empty($validated['id_issue_country'])) {
            $validated['id_issue_country'] = strtoupper($validated['id_issue_country']);
        }

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('guests', 'public');
            $validated['profile_image'] = $path;
        }

        $guest = Guest::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.guest_created_successfully'),
            'guest' => [
                'id' => $guest->id,
                'name' => $guest->full_name,
                'mobile' => $guest->mobile,
                'email' => $guest->email,
            ]
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $guests = $this->visibleGuests($request)
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('id_number', 'like', "%{$query}%")
                  ->orWhere('mobile_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get([
                'id',
                'first_name',
                'second_name',
                'middle_name',
                'last_name',
                'mobile_dial_code',
                'mobile_number',
                'email',
                'id_number',
            ]);

        return response()->json($guests);
    }

    public function show(Request $request, Guest $guest)
    {
        $guest = $this->visibleGuests($request)->findOrFail($guest->id);

        return response()->json($guest);
    }

    public function update(Request $request, Guest $guest): JsonResponse
    {
        $guest = $this->visibleGuests($request)->findOrFail($guest->id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'second_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'guest_class_id' => 'nullable|exists:guest_classes,id',
            'nationality' => 'nullable|string|max:100',
            'nationality_code' => 'nullable|string|size:3|regex:/^[A-Za-z]{3}$/',
            'guest_type' => 'nullable|in:individual,family,corporate',
            'id_type' => 'nullable|in:national_id,passport,iqama,driver_license',
            'id_number' => 'nullable|string|max:50',
            'id_issue_country' => 'nullable|string|max:10',
            'id_expiry_date' => 'nullable|date',
            'visa_number' => 'nullable|string|max:50',
            'arrival_from' => 'nullable|string|max:100',
            'id_serial' => 'nullable|in:first,second,third,last',
            'mobile_dial_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'work_place' => 'nullable|string|max:200',
            'work_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'car_license_plate' => 'nullable|string|max:50',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (! empty($validated['nationality_code'])) {
            $validated['nationality_code'] = strtoupper($validated['nationality_code']);
        }

        if (! empty($validated['id_issue_country'])) {
            $validated['id_issue_country'] = strtoupper($validated['id_issue_country']);
        }

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($guest->profile_image && \Storage::disk('public')->exists($guest->profile_image)) {
                \Storage::disk('public')->delete($guest->profile_image);
            }

            $file = $request->file('profile_image');
            $path = $file->store('guests', 'public');
            $validated['profile_image'] = $path;
        }

        $guest->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.guest_updated_successfully'),
        ]);
    }

    public function destroy(Request $request, Guest $guest): JsonResponse
    {
        $guest = $this->visibleGuests($request)->findOrFail($guest->id);

        // Delete image if it exists
        if ($guest->profile_image && \Storage::disk('public')->exists($guest->profile_image)) {
            \Storage::disk('public')->delete($guest->profile_image);
        }

        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.guest_deleted_successfully'),
        ]);
    }

    protected function visibleGuests(Request $request): Builder
    {
        return Guest::query();
    }
}
