<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Video extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'course_id', 'module_id', 'title', 'type',
        'duration_seconds', 'sort_order', 'status',
    ];

    public function registerMediaCollections(): void
    {
        // Private disk: video files must never be reachable by a guessable public URL.
        // Access is only granted through VideoStreamController's authorization check.
        $this->addMediaCollection('file')->singleFile()->useDisk('local');
    }

    public function fileUrl(): ?string
    {
        if (!$this->getFirstMedia('file')) {
            return null;
        }

        // Signed + time-limited: the link itself expires and can't be edited,
        // shared or reused outside the page that generated it. Expiry is long
        // enough that pausing mid-lesson won't break playback.
        return URL::temporarySignedRoute('videos.stream', now()->addHours(4), ['video' => $this->id]);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function progress()
    {
        return $this->hasMany(VideoProgress::class);
    }
}
