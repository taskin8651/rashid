<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobPostingController extends Controller
{
    public function index()
    {
        $postings = JobPosting::withCount('applications')->with('course')->latest()->get();
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('admin.careers.index', compact('postings', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        JobPosting::create($validated + ['posted_by' => $request->user()->id]);

        return back()->with('status', 'Job posting created.');
    }

    public function update(Request $request, JobPosting $career)
    {
        $career->update($this->validated($request));

        return back()->with('status', 'Job posting updated.');
    }

    public function toggleStatus(JobPosting $career)
    {
        $career->update(['status' => $career->status === 'open' ? 'closed' : 'open']);

        return back()->with('status', $career->status === 'open' ? 'Job posting reopened.' : 'Job posting closed.');
    }

    public function destroy(JobPosting $career)
    {
        $career->delete();

        return back()->with('status', 'Job posting deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'job_type' => ['nullable', 'string', Rule::in(['Full-time', 'Part-time', 'Internship', 'Freelance'])],
            'work_mode' => ['nullable', 'string', Rule::in(['Onsite', 'Remote', 'Hybrid'])],
            'location' => ['nullable', 'string', 'max:255'],
            'package' => ['nullable', 'string', 'max:100'],
            'vacancies' => ['nullable', 'integer', 'min:1'],
            'apply_by' => ['nullable', 'date'],
        ]);
    }
}
