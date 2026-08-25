<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'report_date', 'task_subject', 'hours_worked', 'status', 'description',
        'attachment_path', 'attachment_original_name',
        'topics_covered', 'students_present', 'homework_assigned',
        'leads_followed_up', 'students_enrolled', 'calls_made',
        'review_status', 'admin_comment', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'hours_worked' => 'decimal:2',
        'homework_assigned' => 'boolean',
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isEditable(): bool
    {
        return $this->review_status === 'pending';
    }
}
