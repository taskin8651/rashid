<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLead extends Model
{
    use HasFactory;

    public const STATUSES = ['new', 'contacted', 'follow_up', 'interested', 'converted', 'lost'];

    public const SOURCES = ['walk_in', 'phone', 'website', 'referral', 'social_media', 'other'];

    protected $fillable = [
        'name', 'phone', 'email', 'course_id', 'franchise_booking_id',
        'source', 'status', 'assigned_to', 'next_follow_up_date',
        'lost_reason', 'converted_enrollment_id', 'created_by',
    ];

    protected $casts = [
        'next_follow_up_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedEnrollment()
    {
        return $this->belongsTo(Enrollment::class, 'converted_enrollment_id');
    }

    public function notes()
    {
        return $this->hasMany(StudentLeadNote::class)->latest();
    }

    /**
     * Digits-only phone number, prefixed with the India country code when the
     * number looks local (10 digits) — wa.me links require the full code.
     */
    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D/', '', $this->phone ?? '');

        return strlen($digits) === 10 ? '91' . $digits : $digits;
    }
}
