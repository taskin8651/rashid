<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'franchise_booking_id', 'completion_date',
        'proof_path', 'notes', 'admin_notes', 'status',
        'certificate_id', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'reviewed_at' => 'datetime',
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

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
