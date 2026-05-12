<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\HallType;
use App\Models\Unit;
use App\Models\UnitClass;
use App\Models\UnitType;
use App\Models\UnitTypeCustomization;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding per-company reservation data...');

        $companies = Company::all();
        foreach ($companies as $company) {
            $this->seedCompanyReservationData($company);
        }

        $this->command->info('Reservation test data seeded successfully.');
    }

    protected function seedCompanyReservationData(Company $company): void
    {
        $unitTypes = UnitType::all();
        $unitClasses = UnitClass::all();
        $hallTypes = HallType::all();
        $propertyType = PropertyType::first();

        // Create unit type customizations per company
        $customizations = [];
        $names = ['Studio', 'One-Bedroom', 'Two-Bedroom'];
        foreach ($unitTypes as $i => $ut) {
            $c = UnitTypeCustomization::firstOrCreate(
                ['company_id' => $company->id, 'name' => $names[$i % count($names)]],
                [
                    'unit_type_id' => $ut->id,
                    'unit_area' => 30 + $i * 20,
                    'single_beds' => 1,
                    'double_beds' => $i > 0 ? 1 : 0,
                    'base_occupancy' => 1 + $i,
                ]
            );
            $customizations[] = $c;
        }

        $branches = Branch::where('company_id', $company->id)->get();

        foreach ($branches as $branch) {
            $property = Property::firstOrCreate(
                ['branch_id' => $branch->id],
                [
                    'company_id' => $company->id,
                    'property_name_en' => $branch->name . ' Property',
                    'property_name_ar' => 'عقار ' . $branch->name,
                    'report_name_en' => $branch->name . ' Property',
                    'report_name_ar' => 'عقار ' . $branch->name,
                    'property_code' => strtoupper(substr($company->name, 0, 3)) . $branch->id,
                    'property_type_id' => $propertyType?->id,
                    'owner_user_id' => 1,
                    'status' => 'active',
                    'phone' => $branch->phone,
                    'email' => $branch->email,
                ]
            );

            $blocks = [];
            foreach (['Block A', 'Block B'] as $name) {
                $blocks[] = Block::firstOrCreate(
                    ['branch_id' => $branch->id, 'name' => $name],
                    ['company_id' => $company->id]
                );
            }

            $floors = [];
            foreach (['Ground Floor', '1st Floor'] as $i => $name) {
                $floors[] = Floor::firstOrCreate(
                    ['branch_id' => $branch->id, 'name' => $name],
                    [
                        'company_id' => $company->id,
                        'block_id' => $blocks[$i % count($blocks)]->id,
                    ]
                );
            }

            $unitPrefix = $branch->id . '-';
            $unitNumbers = [$unitPrefix . '101', $unitPrefix . '102', $unitPrefix . '201', $unitPrefix . '202'];
            $unitClass = $unitClasses->first();
            $hallType = $hallTypes->first();
            foreach ($unitNumbers as $i => $num) {
                $customization = $customizations[$i % count($customizations)];
                Unit::firstOrCreate(
                    ['branch_id' => $branch->id, 'unit_number' => $num],
                    [
                        'company_id' => $company->id,
                        'unit_type_id' => $customization->unit_type_id,
                        'unit_class_id' => $unitClass?->id,
                        'block_id' => $blocks[$i % count($blocks)]->id,
                        'floor_id' => $floors[$i % count($floors)]->id,
                        'hall_type_id' => $hallType?->id,
                        'base_occupancy' => $customization->base_occupancy,
                        'number_of_single_beds' => $customization->single_beds,
                        'number_of_double_beds' => $customization->double_beds,
                        'unit_area' => $customization->unit_area,
                        'is_active' => true,
                        'housekeeping_status' => 'clean',
                    ]
                );
            }

            $guestData = [
                ['first' => 'Ali', 'last' => 'Hassan'],
                ['first' => 'Omar', 'last' => 'Ahmed'],
                ['first' => 'Layla', 'last' => 'Saleh'],
            ];
            $guests = [];
            foreach ($guestData as $g) {
                $email = strtolower($g['first'] . '.' . $g['last'] . $branch->id) . '@guest.com';
                $guests[] = Guest::firstOrCreate(
                    ['branch_id' => $branch->id, 'email' => $email],
                    [
                        'company_id' => $company->id,
                        'first_name' => $g['first'],
                        'last_name' => $g['last'],
                        'gender' => $g['first'] === 'Layla' ? 'female' : 'male',
                        'guest_type' => 'individual',
                        'id_type' => 'national_id',
                        'id_number' => 'ID' . str_pad((string) $branch->id, 6, '0', STR_PAD_LEFT) . rand(100, 999),
                        'mobile_number' => '555' . str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT),
                        'is_active' => true,
                    ]
                );
            }

            $units = Unit::where('branch_id', $branch->id)->get();
            $statuses = ['checked_in', 'confirmed'];

            foreach ($statuses as $i => $status) {
                $unit = $units[$i % $units->count()];
                $guest = $guests[$i % count($guests)];
                $today = Carbon::today();
                $checkIn = $status === 'checked_in' ? (clone $today)->subDays(2) : (clone $today)->addDays(5);
                $checkOut = $status === 'checked_in' ? (clone $today)->addDays(1) : (clone $today)->addDays(7);
                $nights = (int) $checkIn->diffInDays($checkOut);
                $dailyRate = rand(200, 500);
                $totalRent = $dailyRate * $nights;

                Reservation::firstOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'unit_id' => $unit->id,
                        'guest_id' => $guest->id,
                        'check_in_date' => $checkIn->toDateString(),
                    ],
                    [
                        'company_id' => $company->id,
                        'reservation_number' => 'RES-' . $property->id . '-' . $branch->id . '-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                        'check_out_date' => $checkOut->toDateString(),
                        'nights' => $nights,
                        'adults' => 1,
                        'daily_rate' => $dailyRate,
                        'total_rent' => $totalRent,
                        'subtotal' => $totalRent,
                        'grand_total' => $totalRent,
                        'status' => $status,
                        'is_confirmed' => true,
                        'booking_date' => $today->subDays(rand(3, 10)),
                        'created_by' => 1,
                    ]
                );
            }
        }

        $this->command->info("  Seeded reservation data for: {$company->name}");
    }
}
