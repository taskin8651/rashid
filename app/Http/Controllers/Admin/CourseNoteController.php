<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Note;
use Illuminate\Http\Request;

class CourseNoteController extends Controller
{
    public function index(Course $course)
    {
        $course->load('notes');

        return view('admin.courses.notes', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'page_count' => ['nullable', 'integer', 'min:0'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $note = $course->notes()->create([
            'title' => $validated['title'],
            'page_count' => $validated['page_count'] ?? 0,
            'sort_order' => $course->notes()->count(),
        ]);

        $note->addMediaFromRequest('file')->toMediaCollection('file');

        return back()->with('status', 'Note uploaded.');
    }

    public function update(Request $request, Course $course, Note $note)
    {
        abort_unless($note->course_id === $course->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'page_count' => ['nullable', 'integer', 'min:0'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $note->update([
            'title' => $validated['title'],
            'page_count' => $validated['page_count'] ?? 0,
        ]);

        if ($request->hasFile('file')) {
            $note->addMediaFromRequest('file')->toMediaCollection('file');
        }

        return back()->with('status', 'Note updated.');
    }

    public function destroy(Course $course, Note $note)
    {
        abort_unless($note->course_id === $course->id, 404);

        $note->delete();

        return back()->with('status', 'Note deleted.');
    }
}
