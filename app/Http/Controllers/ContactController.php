<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $contactMessage = ContactMessage::create($validated);

        $adminEmails = User::role('admin')->pluck('email');
        if ($adminEmails->isNotEmpty()) {
            try {
                Mail::to($adminEmails->all())->send(new ContactMessageReceived($contactMessage));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'Message sent! We will reply within 24 hours.');
    }
}
