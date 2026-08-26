<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Every admin-panel module and the actions it actually has a route for.
     * Flattened to `{module}-{action}` permission strings by adminPermissions().
     * Only actions with a real route behind them are listed — no permission
     * gets created with nothing to gate.
     *
     * 'follow-up' on leads is the one non-standard action: a lighter-weight
     * permission (view + status/notes, no edit/convert/delete) predating this
     * granular scheme, kept as-is rather than folded into the 5 standard ones.
     */
    public const ADMIN_PERMISSION_MODULES = [
        'leads' => ['index', 'show', 'create', 'edit', 'delete', 'follow-up'],
        'students' => ['index', 'show', 'create', 'edit', 'delete'],
        'courses' => ['index', 'create', 'edit', 'delete'],
        'categories' => ['index', 'create', 'delete'],
        'coupons' => ['index', 'create', 'delete'],
        'payments' => ['index', 'edit'],
        'expenses' => ['index', 'show', 'create', 'delete'],
        'franchise-leads' => ['index', 'show', 'edit'],
        'franchise-resources' => ['index', 'create', 'delete'],
        'gallery' => ['index', 'create', 'edit', 'delete'],
        'reviews' => ['index', 'edit', 'delete'],
        'certificate-applications' => ['index', 'show', 'edit'],
        'certificates' => ['index', 'show', 'create', 'edit'],
        'careers' => ['index', 'create', 'edit', 'delete'],
        'job-applications' => ['index', 'show', 'edit', 'delete'],
        'placements' => ['index', 'show', 'edit', 'delete'],
        'attendance-locations' => ['index', 'create', 'edit', 'delete'],
        'attendance' => ['index'],
        'daily-reports' => ['index', 'edit'],
        'staff' => ['index', 'create', 'edit', 'delete'],
        'faqs' => ['index', 'create', 'edit', 'delete'],
        'blog' => ['index', 'create', 'edit', 'delete'],
        'team' => ['index', 'create', 'edit', 'delete'],
        'team-members' => ['index', 'create', 'edit', 'delete'],
    ];

    public const ADMIN_BASELINE_PERMISSION = 'access-admin-panel';

    /**
     * Maps every permission from the old flat "manage-X" scheme to its
     * granular replacement(s), so existing role assignments carry forward
     * instead of silently disappearing when this runs.
     */
    protected const OLD_TO_NEW = [
        'view-admin-dashboard' => [],
        'manage-leads' => ['leads-index', 'leads-show', 'leads-create', 'leads-edit', 'leads-delete'],
        'follow-up-leads' => ['leads-index', 'leads-show', 'leads-follow-up'],
        'manage-students' => ['students-index', 'students-show', 'students-create', 'students-edit', 'students-delete'],
        'manage-courses' => ['courses-index', 'courses-create', 'courses-edit', 'courses-delete'],
        'manage-categories' => ['categories-index', 'categories-create', 'categories-delete'],
        'manage-coupons' => ['coupons-index', 'coupons-create', 'coupons-delete'],
        'manage-payments' => ['payments-index', 'payments-edit', 'expenses-index', 'expenses-show', 'expenses-create', 'expenses-delete'],
        'manage-franchise-leads' => ['franchise-leads-index', 'franchise-leads-show', 'franchise-leads-edit'],
        'manage-franchise-resources' => ['franchise-resources-index', 'franchise-resources-create', 'franchise-resources-delete'],
        'manage-gallery' => ['gallery-index', 'gallery-create', 'gallery-edit', 'gallery-delete'],
        'manage-reviews' => ['reviews-index', 'reviews-edit', 'reviews-delete'],
        'manage-certificate-applications' => ['certificate-applications-index', 'certificate-applications-show', 'certificate-applications-edit', 'certificates-index', 'certificates-show', 'certificates-create', 'certificates-edit'],
        'manage-placements' => ['placements-index', 'placements-show', 'placements-edit', 'placements-delete'],
        'manage-careers' => ['careers-index', 'careers-create', 'careers-edit', 'careers-delete', 'job-applications-index', 'job-applications-show', 'job-applications-edit', 'job-applications-delete'],
        'manage-attendance' => ['attendance-index'],
        'manage-attendance-locations' => ['attendance-locations-index', 'attendance-locations-create', 'attendance-locations-edit', 'attendance-locations-delete'],
        'manage-daily-reports' => ['daily-reports-index', 'daily-reports-edit', 'staff-index', 'staff-create', 'staff-edit', 'staff-delete'],
        'manage-faqs' => ['faqs-index', 'faqs-create', 'faqs-edit', 'faqs-delete'],
        'manage-blog' => ['blog-index', 'blog-create', 'blog-edit', 'blog-delete'],
        'manage-team' => ['team-index', 'team-create', 'team-edit', 'team-delete'],
        'manage-team-members' => ['team-members-index', 'team-members-create', 'team-members-edit', 'team-members-delete'],
        'manage-admins' => [],
    ];

    public static function adminPermissions(): array
    {
        $permissions = [];

        foreach (self::ADMIN_PERMISSION_MODULES as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = "{$module}-{$action}";
            }
        }

        return $permissions;
    }

    public function run(): void
    {
        $permissions = array_merge(self::adminPermissions(), [self::ADMIN_BASELINE_PERMISSION]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->migrateOldPermissions();

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'franchisee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
    }

    /**
     * One-time-per-old-permission migration: every role holding an old
     * "manage-X" permission gets the equivalent new granular permissions,
     * then the old permission rows are removed. Safe to re-run — once the
     * old rows are gone there's nothing left to migrate.
     */
    protected function migrateOldPermissions(): void
    {
        foreach (self::OLD_TO_NEW as $old => $replacements) {
            $oldPermission = Permission::where('name', $old)->where('guard_name', 'web')->first();

            if (!$oldPermission) {
                continue;
            }

            foreach ($oldPermission->roles as $role) {
                if ($replacements) {
                    $role->givePermissionTo($replacements);
                }
            }

            $oldPermission->delete();
        }
    }
}
