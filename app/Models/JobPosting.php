<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'company_name', 'description', 'requirements',
        'job_type', 'work_mode', 'location', 'package', 'vacancies',
        'apply_by', 'status', 'posted_by',
    ];

    protected $casts = [
        'apply_by' => 'date',
        'vacancies' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where(fn ($q) => $q->whereNull('apply_by')->orWhere('apply_by', '>=', now()->toDateString()));
    }
}
