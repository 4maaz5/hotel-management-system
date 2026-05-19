<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyAdditionalDetail;
use App\Models\PropertyCommercialDetail;
use App\Models\PropertyPhoto;
use App\Models\PropertyTourismLicense;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PropertyInfoController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with([
            'tourismLicense',
            'commercialDetail',
            'photos',
        ]);

        if ($tenantId = $this->tenantIdForUser($request->user())) {
            $query->where('company_id', $tenantId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_name_en', 'like', '%'.$request->search.'%')
                    ->orWhere('property_code', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $properties = $query->paginate(10)->withQueryString();

        $properties->getCollection()->transform(function ($property) {

            $tourismFields = [
                $property->tourismLicense?->tourism_activity_type,
                $property->tourismLicense?->license_number,
                $property->tourismLicense?->license_expiry_date,
                $property->tourismLicense?->number_of_rooms,
                $property->tourismLicense?->license_file_path,
            ];

            $tourismProgress = round((collect($tourismFields)->filter()->count() / 5) * 100);

            $commercialFields = [
                $property->commercialDetail?->registration_number,
                $property->commercialDetail?->activity_license_number,
                $property->commercialDetail?->vat_registration_number,
                $property->commercialDetail?->registration_file_path,
            ];

            $commercialProgress = round((collect($commercialFields)->filter()->count() / 4) * 100);

            $photoCount = $property->photos->count();

            return [
                'property' => $property,
                'tourism_progress' => $tourismProgress,
                'commercial_progress' => $commercialProgress,
                'photo_count' => $photoCount,
            ];
        });

        return view('admin.propertyInfo.index', compact('properties'));
    }

    public function edit(Request $request, ?Property $property = null)
    {
        $property = $this->propertyForRequest($request, $property, [
            'tourismLicense',
            'commercialDetail',
            'additionalDetail',
            'photos',
        ]);

        return view('admin.propertyInfo.edit', compact('property'));
    }

    public function savePropertyDetails(Request $request, ?Property $property = null)
    {
        $property = $this->propertyForRequest($request, $property, [
            'tourismLicense',
            'commercialDetail',
            'additionalDetail',
            'photos',
        ]);

        $tenantId = $this->tenantIdForUser($request->user()) ?: $property->company_id;
        abort_unless($tenantId && (int) $tenantId === (int) $property->company_id, 404);

        $request->validate([
            'unitClass' => ['required', Rule::in([
                'hotel',
                'serviced_apartment',
                'camp',
                'holiday_house',
                'hostel',
                'apartment_hotel',
                'resort',
                'hotel_villa',
                'heritage_hotel',
                'pop_up_accommodation',
            ])],
            'Tourismlicensenumber' => 'required|string|max:15',
            'tourismLicenseExpDate' => 'required|date',
            'file-upload' => 'nullable|file|mimes:pdf,tiff|max:10240',
            'CommercialRegistrationNumber' => 'required|string|max:50',
            'taxRegistrationNo' => 'required|numeric',
            'file-upload-2' => 'nullable|file|mimes:pdf,tiff|max:10240',
            'photos.*' => 'nullable|image|mimes:jpeg,png|max:750',
            'NoOfRooms' => 'nullable|integer|min:0',
            'NoOfBeds' => 'nullable|integer|min:0',
            'distancefromHaram' => 'nullable|numeric|min:0',
        ]);

        $request->validate([
            'Tourismlicensenumber' => [
                Rule::unique('property_tourism_licenses', 'license_number')
                    ->where(fn ($query) => $query->where('company_id', $tenantId))
                    ->ignore($this->propertyTourismLicense($property)?->id),
            ],
            'CommercialRegistrationNumber' => [
                Rule::unique('property_commercial_details', 'registration_number')
                    ->where(fn ($query) => $query->where('company_id', $tenantId))
                    ->ignore($this->propertyCommercialDetail($property)?->id),
            ],
        ]);

        $before = $this->propertyInfoActivityData($property);
        $propertyId = $property->id;
        $companyId = $property->company_id;
        $branchId = $property->branch_id;

        DB::transaction(function () use ($request, $property, $companyId, $branchId) {

            $tourismData = [
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'tourism_activity_type' => $request->unitClass,
                'license_number' => $request->Tourismlicensenumber,
                'license_expiry_date' => $request->tourismLicenseExpDate,
                'number_of_rooms' => $request->NoOfRooms,
                'number_of_beds' => $request->NoOfBeds,
            ];

            if ($request->hasFile('file-upload')) {
                $tourismData['license_file_path'] = $request->file('file-upload')
                    ->store('property/license', 'public');
            }

            PropertyTourismLicense::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $companyId, 'branch_id' => $branchId],
                $tourismData
            );

            $commercialData = [
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'registration_number' => $request->CommercialRegistrationNumber,
                'activity_license_number' => $request->CommActivityLicenseNo,
                'vat_registration_number' => $request->taxRegistrationNo,
            ];

            if ($request->hasFile('file-upload-2')) {
                $commercialData['registration_file_path'] = $request->file('file-upload-2')
                    ->store('property/commercial', 'public');
            }

            PropertyCommercialDetail::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $companyId, 'branch_id' => $branchId],
                $commercialData
            );

            PropertyAdditionalDetail::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $companyId, 'branch_id' => $branchId],
                [
                    'distance_from_haram_km' => $request->distancefromHaram,
                    'description_en' => $request->description,
                    'description_ar' => $request->description,
                ]
            );

            if ($request->hasFile('photos')) {

                foreach ($request->file('photos') as $index => $photo) {

                    $photoPath = $photo->store(
                        'property/photos',
                        'public'
                    );

                    PropertyPhoto::create([
                        'company_id' => $companyId,
                        'branch_id' => $branchId,
                        'photo_path' => $photoPath,
                        'photo_order' => $index,
                        'is_main' => $index === 0,
                    ]);
                }
            }
        });

        $refreshedProperty = Property::query()->with([
            'tourismLicense',
            'commercialDetail',
            'additionalDetail',
            'photos',
        ])->findOrFail($propertyId);

        app(UserActivityLogger::class)->log(
            'property_setup',
            'updated',
            $refreshedProperty,
            "Updated property setup details for {$refreshedProperty->property_name_en}",
            $before,
            $this->propertyInfoActivityData($refreshedProperty),
            ['area' => 'property_details'],
            $refreshedProperty->id,
            $refreshedProperty->property_code
        );

        return redirect()->route('setup-sidebar.property-info.index')->with('success', __('messages.property_details_saved_successfully'));
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id()
            ?: $user?->company_id
            ?: $user?->branch?->company_id;
    }

    private function propertyForRequest(Request $request, ?Property $property = null, array $relations = []): Property
    {
        $requestedPropertyId = $property?->id ?: $request->integer('property_id');
        $user = $request->user();

        if ($requestedPropertyId && $user) {
            $query = $user->accessiblePropertiesQuery();

            if ($relations !== []) {
                $query->with($relations);
            }

            $property = $query->whereKey($requestedPropertyId)->first();
            abort_unless($property, 404);

            return $property;
        }

        abort_unless(app(PropertyContext::class)->branchId(), 404);

        $property = Property::current($relations);
        abort_unless($property, 404);

        return $property;
    }

    private function propertyTourismLicense(Property $property): ?PropertyTourismLicense
    {
        return PropertyTourismLicense::withoutGlobalScopes()
            ->where('company_id', $property->company_id)
            ->where('branch_id', $property->branch_id)
            ->first();
    }

    private function propertyCommercialDetail(Property $property): ?PropertyCommercialDetail
    {
        return PropertyCommercialDetail::withoutGlobalScopes()
            ->where('company_id', $property->company_id)
            ->where('branch_id', $property->branch_id)
            ->first();
    }

    protected function propertyInfoActivityData(Property $property): array
    {
        return [
            'property_code' => $property->property_code,
            'tourism_license_number' => $property->tourismLicense?->license_number,
            'tourism_license_expiry' => optional($property->tourismLicense?->license_expiry_date)->format('Y-m-d'),
            'commercial_registration_number' => $property->commercialDetail?->registration_number,
            'vat_registration_number' => $property->commercialDetail?->vat_registration_number,
            'distance_from_haram_km' => $property->additionalDetail?->distance_from_haram_km,
            'photo_count' => $property->photos?->count() ?? 0,
        ];
    }
}
