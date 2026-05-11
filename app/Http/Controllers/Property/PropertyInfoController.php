<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyAdditionalDetail;
use App\Models\PropertyCommercialDetail;
use App\Models\PropertyPhoto;
use App\Models\PropertyTourismLicense;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyInfoController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with([
            'tourismLicense',
            'commercialDetail',
            'photos',
        ]);
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

    public function edit()
    {
        $property = Property::current([
            'tourismLicense',
            'commercialDetail',
            'photos',
        ]);

        return view('admin.propertyInfo.edit', compact('property'));
    }

    public function savePropertyDetails(Request $request)
    {
        $request->validate([
            'Tourismlicensenumber' => 'required|string|max:15',
            'tourismLicenseExpDate' => 'required|date',
            'file-upload' => 'nullable|file|mimes:pdf,tiff|max:10240',
            'CommercialRegistrationNumber' => 'required|numeric',
            'taxRegistrationNo' => 'required|numeric',
            'file-upload-2' => 'nullable|file|mimes:pdf,tiff|max:10240',
            'photos.*' => 'nullable|image|mimes:jpeg,png|max:750',
        ]);

        $property = Property::current();
        abort_unless($property, 404);
        $before = $this->propertyInfoActivityData($property->load([
            'tourismLicense',
            'commercialDetail',
            'additionalDetail',
            'photos',
        ]));
        $propertyId = $property->id;

        DB::transaction(function () use ($request, $propertyId) {

            $tourismFilePath = null;

            if ($request->hasFile('file-upload')) {
                $tourismFilePath = $request->file('file-upload')
                    ->store('property/license', 'public');
            }

            PropertyTourismLicense::updateOrCreate(
                ['property_id' => $propertyId],
                [
                    'tourism_activity_type' => $request->unitClass,
                    'license_number' => $request->Tourismlicensenumber,
                    'license_expiry_date' => $request->tourismLicenseExpDate,
                    'number_of_rooms' => $request->NoOfRooms,
                    'number_of_beds' => $request->NoOfBeds,
                    'license_file_path' => $tourismFilePath,
                ]
            );

            $commercialFilePath = null;

            if ($request->hasFile('file-upload-2')) {
                $commercialFilePath = $request->file('file-upload-2')
                    ->store('property/commercial', 'public');
            }

            PropertyCommercialDetail::updateOrCreate(
                ['property_id' => $propertyId],
                [
                    'registration_number' => $request->CommercialRegistrationNumber,
                    'activity_license_number' => $request->CommActivityLicenseNo,
                    'vat_registration_number' => $request->taxRegistrationNo,
                    'registration_file_path' => $commercialFilePath,
                ]
            );

            PropertyAdditionalDetail::updateOrCreate(
                ['property_id' => $propertyId],
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
                        'property_id' => $propertyId,
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
