<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'franchise_booking_id',
        'company_name', 'job_title', 'job_type', 'work_mode', 'location',
        'package', 'joining_date', 'offer_letter_path', 'linkedin_url', 'testimonial',
        'is_featured', 'status', 'admin_notes', 'submitted_by', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'reviewed_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
