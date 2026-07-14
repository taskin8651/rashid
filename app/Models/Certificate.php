<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['user_id', 'course_id', 'cert_code', 'issued_date', 'status'];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf')->singleFile();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RTC-' . now()->format('y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (self::where('cert_code', $code)->exists());

        return $code;
    }
}
