<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permissions an admin-team role can be granted from the Team
     * Management screen. Kept separate from ADMIN_BASELINE_PERMISSION,
     * which every admin-side role gets automatically regardless of what's
     * toggled — it's what actually lets them into /admin at all.
     */
    public const ADMIN_PERMISSIONS = [
        'view-admin-dashboard',
        'manage-leads',
        'follow-up-leads',
        'manage-students',
        'manage-courses',
        'manage-categories',
        'manage-coupons',
        'manage-payments',
        'manage-franchise-leads',
        'manage-franchise-resources',
        'manage-gallery',
        'manage-reviews',
        'manage-certificate-applications',
        'manage-placements',
        'manage-careers',
        'manage-attendance',
        'manage-attendance-locations',
        'manage-faqs',
        'manage-blog',
        'manage-team',
        'manage-team-members',
    ];

    public const ADMIN_BASELINE_PERMISSION = 'access-admin-panel';

    public function run(): void
    {
        $permissions = array_merge(self::ADMIN_PERMISSIONS, [self::ADMIN_BASELINE_PERMISSION]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'franchisee', 'guard_name' => 'web']);
    }
}
