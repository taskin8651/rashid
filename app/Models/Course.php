<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
