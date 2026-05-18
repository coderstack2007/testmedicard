<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Branch;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing branch or create new one
        $branch = Branch::firstOrCreate([
            'name' => 'Main Medical Center',
        ], [
            'address' => '123 Medical St, Health City',
            'is_active' => true,
            'code' => 'MAIN',
        ]);

        Department::create([
            'branch_id'   => $branch->id,
            'name'        => 'General Medicine',
            'description' => 'General medical services',
        ]);
    }
}