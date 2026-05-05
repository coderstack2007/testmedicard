<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Cardiology',
            'Neurology',
            'Pediatrics',
            'General Surgery',
            'Orthopedics',
            'Dermatology',
            'Ophthalmology',
            'ENT (Otolaryngology)',
            'Gynecology',
            'Urology',
            'Psychiatry',
            'Endocrinology',
            'Gastroenterology',
            'Pulmonology',
            'Nephrology',
            'Oncology',
            'Rheumatology',
            'Anesthesiology',
            'Radiology',
            'Emergency Medicine',
            'Family Medicine',
            'Internal Medicine',
            'Infectious Disease',
            'Hematology',
            'Plastic Surgery',
        ];

        foreach ($specializations as $name) {
            DB::table('specializations')->insertOrIgnore(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}