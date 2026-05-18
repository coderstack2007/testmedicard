<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test admin user (role_id = 1)
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@medicard.local',
            'password' => bcrypt('password'),
            'role_id'  => 1,
            'is_active' => true,
        ]);

        // Create test moderator (role_id = 2)
        User::create([
            'name'     => 'Moderator User',
            'email'    => 'moderator@medicard.local',
            'password' => bcrypt('password'),
            'role_id'  => 2,
            'is_active' => true,
        ]);
    }
}
