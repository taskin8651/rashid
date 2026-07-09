<?php

namespace App\Http\Controllers\Admin;

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

        $students = $query->latest()->paginate(20)->withQueryString();

        return view('admin.students.index', compact('students', 'search'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $student->update($validated);

        return back()->with('status', 'Student updated.');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return back()->with('status', 'Student deleted.');
    }
}
