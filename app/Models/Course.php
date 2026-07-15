<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id', 'franchise_booking_id', 'name', 'slug', 'price', 'duration_text',
        'description', 'rating', 'rating_count', 'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // SEO title/description are never form fields — they're derived
        // automatically from the course name and description whenever either
        // changes, so admins/franchisees never have to think about SEO.
        static::saving(function (Course $course) {
            if ($course->isDirty('name') || !$course->meta_title) {
                $course->meta_title = $course->name . ' Course | R-Tech Computer';
            }

            if ($course->isDirty('description') || !$course->meta_description) {
                $course->meta_description = $course->description
                    ? Str::limit(strip_tags($course->description), 155)
                    : 'Learn ' . $course->name . ' with R-Tech Computer — practical, job-oriented training with live projects and placement support.';
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function ownerLabel(): string
    {
        return $this->franchise_booking_id
            ? '🏙 ' . $this->franchiseBooking->city . ' Franchise'
            : 'R-Tech Official';
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('sort_order');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function notes()
    {
        return $this->hasMany(Note::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->getFirstMediaUrl('thumbnail') ?: null;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function averageRating(): float
    {
        $count = $this->approvedReviews()->count();

        return $count > 0
            ? round((float) $this->approvedReviews()->avg('rating'), 1)
            : (float) $this->rating;
    }

    public function reviewCount(): int
    {
        $count = $this->approvedReviews()->count();

        return $count > 0 ? $count : (int) $this->rating_count;
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->name . ' Course | R-Tech Computer';
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags((string) $this->description), 155);
    }

    public function seoImage(): ?string
    {
        return $this->thumbnailUrl();
    }
}
