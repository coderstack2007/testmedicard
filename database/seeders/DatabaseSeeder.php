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
        $this->call([
            RolesSeeder::class,
            BranchSeeder::class,
            DepartmentSeeder::class,
            SpecializationSeeder::class,
            UsersSeeder::class,
            PermissionsSeeder::class,
        ]);

    }
}

