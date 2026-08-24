<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        $members = User::role(['staff', 'teacher'])->with('roles')->withCount('dailyReports')->get();

        return view('admin.staff.index', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['staff', 'teacher'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->assignRole($validated['role']);

        return back()->with('status', 'Staff/teacher account created.');
    }

    public function update(Request $request, User $member)
    {
        abort_unless($member->hasAnyRole(['staff', 'teacher']), 404);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['staff', 'teacher'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $member->syncRoles([$validated['role']]);
        $member->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Account updated.');
    }

    public function destroy(User $member)
    {
        abort_unless($member->hasAnyRole(['staff', 'teacher']), 404);

        $member->syncRoles([]);

        return back()->with('status', 'Account removed.');
    }
}
