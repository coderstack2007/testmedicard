<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create a default department
        Department::create([
            'branch_id'   => $branch->id,
            'name'        => 'General Medicine',
            'description' => 'General medical services',
        ]);
    }
}
