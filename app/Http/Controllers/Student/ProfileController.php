<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $profile = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'date_of_birth' => optional($user->date_of_birth)->format('Y-m-d'),
            'guardian_name' => $user->guardian_name,
            'blood_group' => $user->blood_group,
            'emergency_contact' => $user->emergency_contact,
        ];

        return view('student.profile', compact('profile', 'user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photo = $request->hasFile('photo');
        unset($validated['photo']);

        $user->update($validated);

        if ($photo) {
            $user->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return back()->with('status', 'Profile updated!');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => $validated['password']]);

        return back()->with('status', 'Password changed successfully.');
    }
}
