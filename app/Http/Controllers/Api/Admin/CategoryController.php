<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('courses')->get()->map(fn (Category $c) => [
            'id' => $c->id,
            'name' => $c->name,
            'icon' => $c->icon,
            'courses_count' => $c->courses_count,
            'status' => $c->status,
        ]);

        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:10'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json(['message' => 'Category created.', 'category_id' => $category->id], 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:10'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $category->update($validated);

        return response()->json(['message' => 'Category updated.']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted.']);
    }
}
