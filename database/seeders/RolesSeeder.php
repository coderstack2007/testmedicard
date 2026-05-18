<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'absolute_admin',
                'display_name' => 'Absolute Admin',
                'description'  => 'Full access to every resource across all branches and departments.',
            ],
            [
                'name'         => 'moderator',
                'display_name' => 'Moderator',
                'description'  => 'Full access scoped to their own branch and its departments.',
            ],
            [
                'name'         => 'doctor',
                'display_name' => 'Doctor',
                'description'  => 'Can view colleagues in the same department and read patient medical cards.',
            ],
            [
                'name'         => 'patient',
                'display_name' => 'Patient',
                'description'  => 'Can only read their own medical card (diagnoses).',
            ],
        ];

        // ✅ Было забыто: без insert() данные никуда не сохраняются
        DB::table('roles')->insertOrIgnore(
            array_map(fn($r) => array_merge($r, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $roles)
        );
    }
}