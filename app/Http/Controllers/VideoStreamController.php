<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class VideoStreamController extends Controller
{
    public function stream(Video $video)
    {
        abort_unless($video->status === 'active', 404);

        $media = $video->getFirstMedia('file');
        abort_unless($media, 404);

        if ($video->type !== 'demo' && !$this->canAccessPremium($video)) {
            abort(403, 'Enroll in this course to watch this video.');
        }

        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
        ]);
    }

    protected function canAccessPremium(Video $video): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $course = $video->course;

        if ($course->franchise_booking_id && $course->franchiseBooking->user_id === $user->id) {
            return true;
        }

        return $user->enrollments()
            ->where('course_id', $video->course_id)
            ->whereIn('status', ['paid', 'completed'])
            ->exists();
    }
}
