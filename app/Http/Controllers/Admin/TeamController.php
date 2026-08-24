<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    protected function adminRoleNames(): array
    {
        return Role::whereNotIn('name', ['student', 'franchisee', 'staff', 'teacher'])->pluck('name')->all();
    }

    public function index()
    {
        $roles = Role::whereNotIn('name', ['student', 'franchisee', 'staff', 'teacher'])
            ->with('permissions')
            ->withCount('users')
            ->get();

        $members = User::role($this->adminRoleNames())->with('roles')->get();

        return view('admin.team.index', [
            'roles' => $roles,
            'members' => $members,
            'availablePermissions' => RolesAndPermissionsSeeder::ADMIN_PERMISSIONS,
        ]);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(RolesAndPermissionsSeeder::ADMIN_PERMISSIONS)],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions(array_merge($validated['permissions'] ?? [], [RolesAndPermissionsSeeder::ADMIN_BASELINE_PERMISSION]));

        return back()->with('status', 'Role created.');
    }

    public function updateRole(Request $request, Role $role)
    {
        abort_if($role->name === 'admin', 403, 'The default Admin role cannot be edited.');

        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => [Rule::in(RolesAndPermissionsSeeder::ADMIN_PERMISSIONS)],
        ]);

        $role->syncPermissions(array_merge($validated['permissions'] ?? [], [RolesAndPermissionsSeeder::ADMIN_BASELINE_PERMISSION]));

        return back()->with('status', 'Role updated.');
    }

    public function destroyRole(Role $role)
    {
        abort_if($role->name === 'admin', 403, 'The default Admin role cannot be deleted.');
        abort_if($role->users()->count() > 0, 422, 'Reassign team members off this role before deleting it.');

        $role->delete();

        return back()->with('status', 'Role deleted.');
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in($this->adminRoleNames())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->assignRole($validated['role']);

        return back()->with('status', 'Team member added.');
    }

    public function updateMember(Request $request, User $member)
    {
        abort_unless($member->hasAnyRole($this->adminRoleNames()), 404);

        $validated = $request->validate([
            'role' => ['required', Rule::in($this->adminRoleNames())],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($member->hasRole('admin') && $validated['role'] !== 'admin' && User::role('admin')->count() <= 1) {
            return back()->withErrors(['role' => 'You cannot remove the last remaining Admin — assign someone else first.']);
        }

        $member->syncRoles([$validated['role']]);
        $member->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Team member updated.');
    }

    public function destroyMember(User $member)
    {
        abort_unless($member->hasAnyRole($this->adminRoleNames()), 404);

        if ($member->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->withErrors(['role' => 'You cannot remove the last remaining Admin.']);
        }

        $member->syncRoles([]);

        return back()->with('status', 'Team member removed from the admin panel.');
    }
}
