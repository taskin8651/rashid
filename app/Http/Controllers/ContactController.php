<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Course;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        $courses = Course::where('status', 'active')->get();

        return view('contact', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'interested_course' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        ContactMessage::create($validated);

        return back()->with('status', 'Message sent! We will reply within 24 hours.');
    }
}
