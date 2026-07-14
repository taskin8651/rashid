<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\VideoProgress;
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

    public function show(User $student)
    {
        abort_unless($student->hasRole('student'), 404);

        $enrollments = $student->enrollments()->with(['course', 'payment'])->latest('enrolled_at')->get();

        $courses = $enrollments->map(function ($e) use ($student) {
            $totalVideos = $e->course->videos()->where('status', 'active')->whereHas('media')->count();
            $watched = VideoProgress::where('user_id', $student->id)
                ->whereIn('video_id', $e->course->videos()->where('status', 'active')->whereHas('media')->pluck('id'))
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return ['enrollment' => $e, 'course' => $e->course, 'percent' => $percent];
        });

        $certificates = $student->certificates()->with('course')->get();
        $quizAttempts = $student->quizAttempts()->with('course')->latest('submitted_at')->get();
        $payments = Payment::where('user_id', $student->id)->latest()->get();

        $stats = [
            'courses' => $enrollments->count(),
            'certificates' => $certificates->where('status', 'issued')->count(),
            'videos_watched' => $student->videoProgress()->where('completed', true)->count(),
            'total_spent' => $payments->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.students.show', compact('student', 'courses', 'certificates', 'quizAttempts', 'payments', 'stats'));
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
