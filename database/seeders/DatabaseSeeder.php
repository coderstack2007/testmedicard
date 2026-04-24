<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create main branch
        $branch = Branch::create([
            'name'    => 'Main Medical Center',
            'address' => '123 Medical St, Health City',
            'is_active' => true,
            'code' => 'MAIN',
        ]);

        // Create a default department
        Department::create([
            'branch_id'   => $branch->id,
            'name'        => 'General Medicine',
            'description' => 'General medical services',
        ]);

        // Create test admin user
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@medicard.local',
            'password' => bcrypt('password'),
            'role'     => UserRole::AbsoluteAdmin,
            'is_active' => true,
        ]);

        // Create test moderator
        User::create([
            'name'     => 'Moderator User',
            'email'    => 'moderator@medicard.local',
            'password' => bcrypt('password'),
            'role'     => UserRole::Moderator,
            'is_active' => true,
        ]);
    }
}

