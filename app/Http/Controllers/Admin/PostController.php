<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author')->latest()->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'cover' => ['nullable', 'image', 'max:5120'],
        ]);

        $slug = Str::slug($validated['title']);
        $original = $slug;
        $i = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $post = Post::create([
            'author_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if ($request->hasFile('cover')) {
            $post->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return back()->with('status', 'Post created.');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'cover' => ['nullable', 'image', 'max:5120'],
        ]);

        $post->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? ($post->published_at ?? now()) : $post->published_at,
        ]);

        if ($request->hasFile('cover')) {
            $post->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return back()->with('status', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('status', 'Post deleted.');
    }
}
