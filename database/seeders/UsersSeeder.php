<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
