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

        $response = response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="video"',
            'X-Content-Type-Options' => 'nosniff',
        ]);

        // Symfony's ResponseHeaderBag recomputes Cache-Control from its own
        // directive state rather than keeping a raw string, so these must be
        // set via its API to actually stick (a plain header string gets
        // silently rewritten, e.g. "private" reverting to "public").
        $response->setPrivate();
        $response->setMaxAge(0);
        $response->headers->addCacheControlDirective('no-store');

        return $response;
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
