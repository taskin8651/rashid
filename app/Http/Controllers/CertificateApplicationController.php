<?php

namespace App\Http\Controllers;

use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\User;
use App\Notifications\CertificateApplicationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CertificateApplicationController extends Controller
{
    public function create()
    {
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        $franchises = FranchiseBooking::where('status', 'paid')->orderBy('city')->get(['id', 'city']);

        return view('certificate-applications.create', compact('courses', 'franchises'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'course_id' => ['required', 'exists:courses,id'],
            'franchise_booking_id' => ['nullable', 'exists:franchise_bookings,id'],
            'completion_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'terms' => ['accepted'],
        ];

        if (!Auth::check()) {
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
            ]);
            Auth::login($user);
            $request->session()->regenerate();
        }

        if (!$user->hasAnyRole(['student', 'admin', 'franchisee'])) {
            $user->assignRole('student');
        }

        $proofPath = $request->hasFile('proof')
            ? $request->file('proof')->store('certificate-proofs', 'local')
            : null;

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $validated['course_id'],
            'franchise_booking_id' => $validated['franchise_booking_id'] ?? null,
            'completion_date' => $validated['completion_date'],
            'proof_path' => $proofPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        foreach (User::role('admin')->get() as $admin) {
            $admin->notify(new CertificateApplicationSubmittedNotification($application->load(['user', 'course'])));
        }

        return redirect()->route('certificate-applications.status')->with('status', 'Application submitted! We will review it and notify you once your certificate is ready.');
    }

    public function status(Request $request)
    {
        $applications = $request->user()
            ->certificateApplications()
            ->with(['course', 'certificate'])
            ->latest()
            ->get();

        return view('certificate-applications.status', compact('applications'));
    }
}
