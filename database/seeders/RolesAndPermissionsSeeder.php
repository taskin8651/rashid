<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-courses',
            'manage-categories',
            'manage-batches',
            'manage-coupons',
            'manage-students',
            'manage-payments',
            'manage-videos',
            'manage-assignments',
            'manage-quizzes',
            'manage-notes',
            'manage-certificates',
            'manage-franchise-leads',
            'manage-contact-messages',
            'view-admin-dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'franchisee', 'guard_name' => 'web']);
    }
}
