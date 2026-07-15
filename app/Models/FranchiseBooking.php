<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseBooking extends Model
{
    use HasFactory;

    public const STAGES = [
        'registered' => 'Registered',
        'contacted' => 'Discussion Call Done',
        'site_visit' => 'Site Visit Scheduled',
        'agreement' => 'Agreement Signed',
        'training' => 'Training in Progress',
        'launched' => 'Launched',
    ];

    protected $fillable = ['franchise_lead_id', 'user_id', 'name', 'email', 'phone', 'city', 'amount', 'status', 'stage'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(FranchiseLead::class, 'franchise_lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'payable_id')->where('payable_type', 'franchise_booking');
    }

    public function documents()
    {
        return $this->hasMany(FranchiseDocument::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class);
    }

    public function galleryImages()
    {
        return $this->hasMany(GalleryImage::class);
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function stageIndex(): int
    {
        $keys = array_keys(self::STAGES);
        $index = array_search($this->stage, $keys, true);

        return $index === false ? 0 : $index;
    }
}
