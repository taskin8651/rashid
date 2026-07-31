<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'cert_code', 'roll_no', 'father_name', 'batch_name', 'issued_date', 'status',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects()
    {
        return $this->hasMany(CertificateSubject::class)->orderBy('sort_order');
    }

    public function hasMarksheetData(): bool
    {
        return $this->subjects()->exists();
    }

    public function totalMaxMarks(): int
    {
        return $this->subjects->sum('max_marks');
    }

    public function totalMarksObtained(): int
    {
        return (int) $this->subjects->sum('marks_obtained');
    }

    public function percentage(): float
    {
        $max = $this->totalMaxMarks();

        return $max > 0 ? round(($this->totalMarksObtained() / $max) * 100, 2) : 0.0;
    }

    public function grade(): string
    {
        $p = $this->percentage();

        return match (true) {
            $p >= 90 => 'A+',
            $p >= 80 => 'A',
            $p >= 70 => 'B+',
            $p >= 60 => 'B',
            $p >= 50 => 'C',
            $p >= 40 => 'D',
            default => 'F',
        };
    }

    public function result(): string
    {
        return $this->percentage() >= 40 ? 'PASS' : 'FAIL';
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RTC-' . now()->format('y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (self::where('cert_code', $code)->exists());

        return $code;
    }
}
