<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Alpha Trading Co.',
                'legal_name' => 'Alpha Trading Company Ltd.',
                'logo' => null,

                'cr_number' => '1010123456',
                'cr_expiry' => Carbon::now()->addYear(),
                'vat_number' => '312345678900003',
                'tax_card_number' => 'TX-001',
                'establishment_id' => 'EST-1001',

                'email' => 'info@alphatrading.com',
                'phone' => '+966500000001',
                'website' => 'https://alphatrading.com',

                'country' => 'Saudi Arabia',
                'city' => 'Riyadh',
                'district' => 'Olaya',
                'street' => 'King Fahad Road',
                'zip_code' => '11564',

                'industry_type' => 'Trading',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beta Services Group',
                'legal_name' => 'Beta Services Group Est.',
                'logo' => null,

                'cr_number' => '1010654321',
                'cr_expiry' => Carbon::now()->addYears(2),
                'vat_number' => '312340000000003',
                'tax_card_number' => 'TX-002',
                'establishment_id' => 'EST-1002',

                'email' => 'contact@betaservices.sa',
                'phone' => '+966500000002',
                'website' => 'https://betaservices.sa',

                'country' => 'Saudi Arabia',
                'city' => 'Jeddah',
                'district' => 'Al Rawdah',
                'street' => 'Prince Sultan Street',
                'zip_code' => '23431',

                'industry_type' => 'Services',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gamma Industrial LLC',
                'legal_name' => 'Gamma Industrial Company LLC',
                'logo' => null,

                'cr_number' => '2050987654',
                'cr_expiry' => Carbon::now()->addMonths(10),
                'vat_number' => '300567890000003',
                'tax_card_number' => 'TX-003',
                'establishment_id' => 'EST-1003',

                'email' => 'support@gammaindustrial.com',
                'phone' => '+966500000003',
                'website' => 'https://gammaindustrial.com',

                'country' => 'Saudi Arabia',
                'city' => 'Dammam',
                'district' => 'Al Faisaliyah',
                'street' => 'King Saud Road',
                'zip_code' => '32272',

                'industry_type' => 'Industrial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delta Construction',
                'legal_name' => 'Delta Construction & Contracting',
                'logo' => null,

                'cr_number' => '2050123987',
                'cr_expiry' => Carbon::now()->addMonths(18),
                'vat_number' => '302345678900003',
                'tax_card_number' => 'TX-004',
                'establishment_id' => 'EST-1004',

                'email' => 'info@deltaconstruction.sa',
                'phone' => '+966500000004',
                'website' => 'https://deltaconstruction.sa',

                'country' => 'Saudi Arabia',
                'city' => 'Khobar',
                'district' => 'Corniche',
                'street' => 'Prince Turki Street',
                'zip_code' => '31952',

                'industry_type' => 'Construction',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Epsilon Tech Solutions',
                'legal_name' => 'Epsilon Technology Solutions Co.',
                'logo' => null,

                'cr_number' => '7012345678',
                'cr_expiry' => Carbon::now()->addYears(3),
                'vat_number' => '309876543200003',
                'tax_card_number' => 'TX-005',
                'establishment_id' => 'EST-1005',

                'email' => 'hello@epsilontech.com',
                'phone' => '+966500000005',
                'website' => 'https://epsilontech.com',

                'country' => 'Saudi Arabia',
                'city' => 'Madinah',
                'district' => 'Al Salam',
                'street' => 'Quba Road',
                'zip_code' => '42312',

                'industry_type' => 'IT Solutions',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($companies as $company) {

            // Insert company and get ID
            $companyId = DB::table('companies')->insertGetId($company);

            // Company documents for each company
            $documents = [
                [
                    'name' => 'Commercial Registration (CR)',
                    'type' => 'CR',
                    'issued_by' => 'Ministry of Commerce',
                    'file_path' => null,
                    'issue_date' => Carbon::now()->subYears(1),
                    'expiration_date' => Carbon::now()->addYears(1),
                    'image' => null,
                ],
                [
                    'name' => 'VAT Certificate',
                    'type' => 'VAT',
                    'issued_by' => 'ZATCA',
                    'file_path' => null,
                    'issue_date' => Carbon::now()->subYears(2),
                    'expiration_date' => Carbon::now()->addYears(2),
                    'image' => null,
                ],
                [
                    'name' => 'Municipality License',
                    'type' => 'Municipality',
                    'issued_by' => 'Baladiyah',
                    'file_path' => null,
                    'issue_date' => Carbon::now()->subYears(1),
                    'expiration_date' => Carbon::now()->addYears(1),
                    'image' => null,
                ],
            ];

            // Insert 1–3 random documents for each company
            foreach (collect($documents)->random(rand(1, 3)) as $doc) {
                DB::table('company_documents')->insert([
                    'company_id' => $companyId,
                    'name' => $doc['name'],
                    'type' => $doc['type'],
                    'issued_by' => $doc['issued_by'],
                    'file_path' => $doc['file_path'],
                    'issue_date' => $doc['issue_date'],
                    'expiration_date' => $doc['expiration_date'],
                    'image' => $doc['image'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
