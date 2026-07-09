<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('student')->with(['enrollments.course']);

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $students = $query->latest()->paginate(20)->through(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'course' => optional($u->enrollments->first())->course->name ?? '—',
            'joined' => $u->created_at->format('d M Y'),
            'status' => $u->is_active ? 'Active' : 'Inactive',
        ]);

        return response()->json($students);
    }

    public function show(User $student)
    {
        $student->load(['enrollments.course', 'certificates']);

        return response()->json(['student' => [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $student->phone,
            'is_active' => $student->is_active,
            'joined' => $student->created_at->format('d M Y'),
            'enrollments' => $student->enrollments->map(fn ($e) => [
                'course' => $e->course->name,
                'status' => $e->status,
            ]),
        ]]);
    }

    public function update(Request $request, User $student)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $student->update($request->only(['name', 'email', 'phone', 'is_active']));

        return response()->json(['message' => 'Student updated.']);
    }

    public function destroy(User $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted.']);
    }
}
