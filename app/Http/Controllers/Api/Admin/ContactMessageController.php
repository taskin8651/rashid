<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->get()->map(fn (ContactMessage $m) => [
            'id' => $m->id,
            'name' => $m->name,
            'email' => $m->email,
            'phone' => $m->phone,
            'interested_course' => $m->interested_course,
            'message' => $m->message,
            'status' => $m->status,
            'created_at' => $m->created_at->format('d M Y'),
        ]);

        return response()->json(['messages' => $messages]);
    }

    public function update(Request $request, ContactMessage $message)
    {
        $request->validate(['status' => ['required', Rule::in(['new', 'read', 'replied'])]]);
        $message->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated.']);
    }
}
