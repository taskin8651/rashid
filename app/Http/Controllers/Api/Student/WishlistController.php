<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $courses = $request->user()->wishlist()->get()->map(fn (Course $c) => [
            'course_id' => $c->id,
            'name' => $c->name,
            'price' => (float) $c->price,
            'thumbnail_url' => $c->thumbnailUrl(),
        ]);

        return response()->json(['wishlist' => $courses]);
    }

    public function toggle(Request $request)
    {
        $request->validate(['course_id' => ['required', 'exists:courses,id']]);

        $user = $request->user();
        $exists = $user->wishlist()->where('course_id', $request->course_id)->exists();

        if ($exists) {
            $user->wishlist()->detach($request->course_id);
            return response()->json(['message' => 'Removed from wishlist', 'wishlisted' => false]);
        }

        $user->wishlist()->attach($request->course_id);
        return response()->json(['message' => 'Added to wishlist', 'wishlisted' => true]);
    }
}
