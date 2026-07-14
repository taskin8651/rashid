<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;

class SubmissionDownloadController extends Controller
{
    public function download(AssignmentSubmission $submission)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $course = $submission->assignment->course;
        $isOwner = $submission->user_id === $user->id;
        $isAdmin = $user->hasRole('admin');
        $isOwningFranchisee = $course->franchise_booking_id && $course->franchiseBooking->user_id === $user->id;

        abort_unless($isOwner || $isAdmin || $isOwningFranchisee, 403);

        $media = $submission->getFirstMedia('file');
        abort_unless($media, 404);

        return response()->download($media->getPath(), $media->file_name);
    }
}
