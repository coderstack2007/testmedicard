<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Create main branch
        $branch = Branch::create([
            'name'    => 'Main Medical Center',
            'address' => '123 Medical St, Health City',
            'is_active' => true,
            'code' => 'MAIN',
        ]);
    }
}
