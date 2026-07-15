<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        Faq::create($validated);

        return back()->with('status', 'FAQ added.');
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $faq->update($validated);

        return back()->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('status', 'FAQ deleted.');
    }
}
