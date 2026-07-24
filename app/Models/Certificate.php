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
        // Private disk: served only through the authorized download route,
        // same pattern as assignment submissions.
        $this->addMediaCollection('pdf')->singleFile()->useDisk('local');
    }

    public function hasUploadedPdf(): bool
    {
        return $this->getFirstMedia('pdf') !== null;
    }

    public function uploadedPdfPath(): ?string
    {
        return $this->getFirstMedia('pdf')?->getPath();
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
