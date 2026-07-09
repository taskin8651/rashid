<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FranchiseResource;
use Illuminate\Http\Request;

class FranchiseResourceController extends Controller
{
    public function index()
    {
        $resources = FranchiseResource::latest()->get();

        return view('admin.franchise.resources', compact('resources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $path = $request->file('file')->store('franchise-resources');

        FranchiseResource::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('status', 'Resource added.');
    }

    public function destroy(FranchiseResource $resource)
    {
        $resource->delete();

        return back()->with('status', 'Resource removed.');
    }
}
