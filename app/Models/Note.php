<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Note extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['course_id', 'title', 'page_count', 'sort_order'];

    public function registerMediaCollections(): void
    {
        // Private disk: course notes are only for enrolled students, admins
        // and the owning franchisee — see NoteDownloadController.
        $this->addMediaCollection('file')->singleFile()->useDisk('local');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
