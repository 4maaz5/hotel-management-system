<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Sample department names
        $departmentNames = [
            'HR', 'Finance', 'IT', 'Sales',
        ];

        $branches = Branch::all(); // Get all branches

        foreach ($branches as $branch) {
            foreach ($departmentNames as $deptName) {
                Department::create([
                    'name' => $deptName,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
