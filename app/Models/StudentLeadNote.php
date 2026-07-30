<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLeadNote extends Model
{
    use HasFactory;

    protected $fillable = ['student_lead_id', 'note', 'created_by'];

    public function studentLead()
    {
        return $this->belongsTo(StudentLead::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
