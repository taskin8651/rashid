<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSubject extends Model
{
    use HasFactory;

    protected $fillable = ['certificate_id', 'subject', 'max_marks', 'marks_obtained', 'sort_order'];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
