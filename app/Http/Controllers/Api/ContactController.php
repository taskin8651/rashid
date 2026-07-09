<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'interested_course' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        ContactMessage::create($request->only(['name', 'email', 'phone', 'interested_course', 'message']));

        return response()->json(['message' => 'Message sent! We will reply within 24 hours.'], 201);
    }
}
