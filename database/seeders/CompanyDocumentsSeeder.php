<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CompanyDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->error('No companies found. Please seed companies first.');

            return;
        }

        $documentNames = [
            'Company Policy',
            'Health & Safety Manual',
            'ISO Certification',
            'Tax Registration',
            'Business License',
            'Financial Report',
            'Employee Handbook',
            'Quality Manual',
            'Audit Report',
            'Environmental Certificate',
            'Insurance Document',
            'Confidentiality Agreement',
            'Trademark Certificate',
            'Patent Registration',
            'Annual Report',
            'Contract Template',
            'GDPR Compliance',
            'Operational Guidelines',
            'Training Manual',
            'Procurement Policy',
        ];

        $documentTypes = ['Policy', 'Manual', 'Certificate', 'Report', 'Agreement', 'Registration'];

        foreach ($companies as $company) {
            // Each company will get 5–10 random documents
            $numDocuments = rand(5, 10);

            $docs = collect($documentNames)->shuffle()->take($numDocuments);

            foreach ($docs as $name) {
                $type = $documentTypes[array_rand($documentTypes)];
                $issuedBy = 'Company HQ';
                $issueDate = Carbon::now()->subYears(rand(0, 5))->subDays(rand(0, 365));
                $expirationDate = Carbon::parse($issueDate)->addYears(rand(1, 5));
                $filePath = "company_documents/{$name}.pdf";

                CompanyDocument::create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'type' => $type,
                    'issued_by' => $issuedBy,
                    'file_path' => $filePath,
                    'issue_date' => $issueDate->toDateString(),
                    'expiration_date' => $expirationDate->toDateString(),
                    'image' => null,
                ]);
            }
        }

        $this->command->info('Company documents seeded successfully for all companies!');
    }
}
