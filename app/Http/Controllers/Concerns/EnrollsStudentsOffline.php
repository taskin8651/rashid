<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait EnrollsStudentsOffline
{
    /**
     * Reuses the existing account if the email already belongs to a student
     * (so re-registering someone doesn't fork their history across two
     * accounts); otherwise creates one. Relies on User's 'password' => 'hashed'
     * cast — never Hash::make() the password before passing it in here.
     */
    protected function createOrFindStudent(array $validated): User
    {
        $student = User::where('email', $validated['email'])->first();

        if ($student) {
            abort_if($student->hasAnyRole(['admin', 'franchisee']), 422, 'This email belongs to a staff account, not a student.');

            return $student;
        }

        if (empty($validated['password'])) {
            throw ValidationException::withMessages(['password' => 'A password is required to create a new student account.']);
        }

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
        ]);

        $student->assignRole('student');

        return $student;
    }

    /**
     * Grants course access immediately (status: paid) regardless of how much
     * of the total fee has actually been collected — offline admissions work
     * "enrol now, pay in installments," unlike the online Razorpay checkout
     * flow where 'paid' only follows a completed payment. Fee/balance
     * tracking lives separately via Enrollment::amount_paid/balance_due,
     * computed from summed Payment rows.
     */
    protected function enrollStudentOffline(User $student, Course $course, float $totalFee, ?array $firstPayment, int $recordedByUserId): Enrollment
    {
        if (! $student->hasRole('student')) {
            $student->assignRole('student');
        }

        $enrollment = Enrollment::firstOrNew(['user_id' => $student->id, 'course_id' => $course->id]);

        if (in_array($enrollment->status, ['paid', 'completed'], true)) {
            throw ValidationException::withMessages(['course_id' => 'This student is already enrolled in that course.']);
        }

        DB::transaction(function () use ($enrollment, $course, $student, $totalFee, $firstPayment, $recordedByUserId) {
            $enrollment->fill([
                'base_price' => $course->price,
                'discount_amount' => max(0, $course->price - $totalFee),
                'final_amount' => $totalFee,
                'status' => 'paid',
                'enrolled_at' => now(),
            ])->save();

            if (! empty($firstPayment['amount'])) {
                Payment::create([
                    'payable_type' => 'course_enrollment',
                    'payable_id' => $enrollment->id,
                    'user_id' => $student->id,
                    'amount' => $firstPayment['amount'],
                    'method' => $firstPayment['method'] ?? 'cash',
                    'note' => $firstPayment['note'] ?? 'Offline registration — initial payment',
                    'recorded_by' => $recordedByUserId,
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);
            }
        });

        return $enrollment;
    }
}
