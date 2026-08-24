<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id', 'report_date', 'task_subject', 'hours_worked', 'status', 'description',
        'attachment_path', 'attachment_original_name',
        'review_status', 'admin_comment', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'hours_worked' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
