<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Permissions ────────────────────────────────────────────────────
        $permissions = [
            // Branches
            ['resource' => 'branches',    'action' => 'view_any', 'scope' => 'global',         'description' => 'List all branches'],
            ['resource' => 'branches',    'action' => 'view',     'scope' => 'global',         'description' => 'View any branch'],
            ['resource' => 'branches',    'action' => 'create',   'scope' => 'global',         'description' => 'Create a branch'],
            ['resource' => 'branches',    'action' => 'update',   'scope' => 'global',         'description' => 'Update any branch'],
            ['resource' => 'branches',    'action' => 'delete',   'scope' => 'global',         'description' => 'Delete any branch'],
            ['resource' => 'branches',    'action' => 'view_any', 'scope' => 'own_branch',     'description' => 'View own branch'],
            ['resource' => 'branches',    'action' => 'update',   'scope' => 'own_branch',     'description' => 'Update own branch'],

            // Departments
            ['resource' => 'departments', 'action' => 'view_any', 'scope' => 'global',         'description' => 'List all departments'],
            ['resource' => 'departments', 'action' => 'view',     'scope' => 'global',         'description' => 'View any department'],
            ['resource' => 'departments', 'action' => 'create',   'scope' => 'global',         'description' => 'Create a department in any branch'],
            ['resource' => 'departments', 'action' => 'update',   'scope' => 'global',         'description' => 'Update any department'],
            ['resource' => 'departments', 'action' => 'delete',   'scope' => 'global',         'description' => 'Delete any department'],
            ['resource' => 'departments', 'action' => 'view_any', 'scope' => 'own_branch',     'description' => 'List departments in own branch'],
            ['resource' => 'departments', 'action' => 'create',   'scope' => 'own_branch',     'description' => 'Create department in own branch'],
            ['resource' => 'departments', 'action' => 'update',   'scope' => 'own_branch',     'description' => 'Update department in own branch'],
            ['resource' => 'departments', 'action' => 'delete',   'scope' => 'own_branch',     'description' => 'Delete department in own branch'],

            // Doctors
            ['resource' => 'doctors',     'action' => 'view_any', 'scope' => 'global',         'description' => 'List all doctors'],
            ['resource' => 'doctors',     'action' => 'view',     'scope' => 'global',         'description' => 'View any doctor profile'],
            ['resource' => 'doctors',     'action' => 'create',   'scope' => 'global',         'description' => 'Create a doctor account'],
            ['resource' => 'doctors',     'action' => 'update',   'scope' => 'global',         'description' => 'Update any doctor profile'],
            ['resource' => 'doctors',     'action' => 'delete',   'scope' => 'global',         'description' => 'Delete any doctor account'],
            ['resource' => 'doctors',     'action' => 'view_any', 'scope' => 'own_branch',     'description' => 'List doctors in own branch'],
            ['resource' => 'doctors',     'action' => 'create',   'scope' => 'own_branch',     'description' => 'Create doctor in own branch'],
            ['resource' => 'doctors',     'action' => 'update',   'scope' => 'own_branch',     'description' => 'Update doctor in own branch'],
            ['resource' => 'doctors',     'action' => 'delete',   'scope' => 'own_branch',     'description' => 'Delete doctor in own branch'],
            ['resource' => 'doctors',     'action' => 'view_any', 'scope' => 'own_department', 'description' => 'View colleagues in same department'],

            // Patients
            ['resource' => 'patients',    'action' => 'view_any', 'scope' => 'global',         'description' => 'List all patients'],
            ['resource' => 'patients',    'action' => 'view',     'scope' => 'global',         'description' => 'View any patient profile'],
            ['resource' => 'patients',    'action' => 'create',   'scope' => 'global',         'description' => 'Create a patient account'],
            ['resource' => 'patients',    'action' => 'update',   'scope' => 'global',         'description' => 'Update any patient profile'],
            ['resource' => 'patients',    'action' => 'delete',   'scope' => 'global',         'description' => 'Delete any patient account'],
            ['resource' => 'patients',    'action' => 'view_any', 'scope' => 'own_branch',     'description' => 'List patients in own branch'],
            ['resource' => 'patients',    'action' => 'create',   'scope' => 'own_branch',     'description' => 'Create patient in own branch'],

            // Diagnoses / Medical Cards
            ['resource' => 'diagnoses',   'action' => 'view_any', 'scope' => 'global',         'description' => 'Read all medical cards'],
            ['resource' => 'diagnoses',   'action' => 'create',   'scope' => 'global',         'description' => 'Create diagnoses for any patient'],
            ['resource' => 'diagnoses',   'action' => 'update',   'scope' => 'global',         'description' => 'Update any diagnosis'],
            ['resource' => 'diagnoses',   'action' => 'delete',   'scope' => 'global',         'description' => 'Delete any diagnosis'],
            ['resource' => 'diagnoses',   'action' => 'view_any', 'scope' => 'own_branch',     'description' => 'Read medical cards in own branch'],
            ['resource' => 'diagnoses',   'action' => 'view',     'scope' => 'own_department', 'description' => "Read medical cards of doctor's patients"],
            ['resource' => 'diagnoses',   'action' => 'create',   'scope' => 'own_department', 'description' => 'Create diagnosis for own patient'],
            ['resource' => 'diagnoses',   'action' => 'view',     'scope' => 'own_record',     'description' => 'Patient reads their own medical card'],

            // Chats
            ['resource' => 'chats',       'action' => 'view_any', 'scope' => 'own_record',     'description' => 'Access own chat threads'],
            ['resource' => 'chats',       'action' => 'create',   'scope' => 'own_record',     'description' => 'Start or reply in own chat thread'],
        ];

        // ✅ insertOrIgnore — безопасно при повторном запуске сидера
        DB::table('permissions')->insertOrIgnore(
            array_map(fn($p) => array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]), $permissions)
        );

        // ── 2. Привязка permissions к ролям ───────────────────────────────────
        $roleMap = DB::table('roles')->pluck('id', 'name');
        $permMap = DB::table('permissions')
            ->get()
            ->keyBy(fn($p) => "{$p->resource}.{$p->action}.{$p->scope}");

        $assign = function (string $roleName, array $permKeys) use ($roleMap, $permMap): void {
            if (! isset($roleMap[$roleName])) {
                return; // роль не найдена — пропускаем
            }
            $roleId = $roleMap[$roleName];
            $pivots = [];
            foreach ($permKeys as $key) {
                if (isset($permMap[$key])) {
                    $pivots[] = [
                        'role_id'       => $roleId,
                        'permission_id' => $permMap[$key]->id,
                    ];
                }
            }
            if ($pivots) {
                DB::table('role_permission')->insertOrIgnore($pivots);
            }
        };

        // AbsoluteAdmin — всё везде
        $assign('absolute_admin', [
            'branches.view_any.global',    'branches.view.global',    'branches.create.global',
            'branches.update.global',      'branches.delete.global',
            'departments.view_any.global', 'departments.view.global', 'departments.create.global',
            'departments.update.global',   'departments.delete.global',
            'doctors.view_any.global',     'doctors.view.global',     'doctors.create.global',
            'doctors.update.global',       'doctors.delete.global',
            'patients.view_any.global',    'patients.view.global',    'patients.create.global',
            'patients.update.global',      'patients.delete.global',
            'diagnoses.view_any.global',   'diagnoses.create.global',
            'diagnoses.update.global',     'diagnoses.delete.global',
            'chats.view_any.own_record',   'chats.create.own_record',
        ]);

        // Moderator — только свой branch
        $assign('moderator', [
            'branches.view_any.own_branch',
            'branches.update.own_branch',
            'departments.view_any.own_branch', 'departments.create.own_branch',
            'departments.update.own_branch',   'departments.delete.own_branch',
            'doctors.view_any.own_branch',     'doctors.create.own_branch',
            'doctors.update.own_branch',       'doctors.delete.own_branch',
            'patients.view_any.own_branch',    'patients.create.own_branch',
            'diagnoses.view_any.own_branch',
            'chats.view_any.own_record',       'chats.create.own_record',
        ]);

        // Doctor — коллеги в отделе + медкарты пациентов
        $assign('doctor', [
            'doctors.view_any.own_department',
            'diagnoses.view.own_department',
            'diagnoses.create.own_department',
            'chats.view_any.own_record',
            'chats.create.own_record',
        ]);

        // Patient — только своя медкарта
        $assign('patient', [
            'diagnoses.view.own_record',
            'chats.view_any.own_record',
            'chats.create.own_record',
        ]);
    }
}