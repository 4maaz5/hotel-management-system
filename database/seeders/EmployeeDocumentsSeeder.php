<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EmployeeDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->count() === 0) {
            $this->command->error('No employees found. Seed employees first.');

            return;
        }

        $documentTypes = ['ID Card', 'Passport', 'Contract', 'License', 'Certificate'];

        foreach ($employees as $employee) {

            $type = $documentTypes[array_rand($documentTypes)];
            $issueDate = Carbon::now()->subYears(rand(0, 5))->subDays(rand(0, 365));
            $expirationDate = Carbon::parse($issueDate)->addYears(rand(1, 5));
            $documentNumber = strtoupper(substr(md5(rand()), 0, 10));
            $filePath = "documents/{$employee->id}/{$type}-document.pdf";

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'type' => $type,
                'file_path' => $filePath,
                'document_number' => $documentNumber,
                'issue_date' => $issueDate->toDateString(),
                'expiration_date' => $expirationDate->toDateString(),
                'image' => null, // optional image path
            ]);
        }

        $this->command->info('Employee documents seeded successfully!');
    }
}
