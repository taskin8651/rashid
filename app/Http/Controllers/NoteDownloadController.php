<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NoteDownloadController extends Controller
{
    public function download(Note $note)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $course = $note->course;
        $isAdmin = $user->hasRole('admin');
        $isOwningFranchisee = $course->franchise_booking_id && $course->franchiseBooking->user_id === $user->id;
        $isEnrolled = $user->enrollments()->where('course_id', $course->id)->whereIn('status', ['paid', 'completed'])->exists();

        abort_unless($isAdmin || $isOwningFranchisee || $isEnrolled, 403);

        $media = $note->getFirstMedia('file');
        abort_unless($media, 404);

        return response()->download($media->getPath(), $media->file_name);
    }
}
