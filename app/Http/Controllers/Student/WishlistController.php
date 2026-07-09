<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()->get();

        return view('student.wishlist', compact('wishlist'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate(['course_id' => ['required', 'exists:courses,id']]);

        $user = $request->user();
        $exists = $user->wishlist()->where('course_id', $validated['course_id'])->exists();

        if ($exists) {
            $user->wishlist()->detach($validated['course_id']);
            return back()->with('status', 'Removed from wishlist.');
        }

        $user->wishlist()->attach($validated['course_id']);
        return back()->with('status', 'Added to wishlist.');
    }
}
