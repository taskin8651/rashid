<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'guardian_name',
        'blood_group',
        'emergency_contact',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
    ];

    public function scopeActiveNow($query)
    {
        return $query->whereNotNull('last_seen_at')->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gte(now()->subMinutes(5));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function photoUrl(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    public function photoPath(): ?string
    {
        $media = $this->getFirstMedia('photo');

        return $media && file_exists($media->getPath()) ? $media->getPath() : null;
    }

    public function ensureStudentCode(): string
    {
        if (!$this->student_code) {
            do {
                $code = 'RTC-' . now()->format('y') . '-' . strtoupper(Str::random(5));
            } while (self::where('student_code', $code)->exists());

            // Deliberately not mass-assignable: a system-issued identifier,
            // never something a form should be able to set.
            $this->forceFill(['student_code' => $code])->save();
        }

        return $this->student_code;
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function franchiseBookings()
    {
        return $this->hasMany(FranchiseBooking::class);
    }

    public function franchiseTeamMemberships()
    {
        return $this->hasMany(FranchiseTeamMember::class);
    }

    /**
     * Every franchise booking this user can act on — ones they own outright,
     * plus ones a franchise owner has added them to as a team member. Owners
     * always have full access; team members are further limited by their
     * FranchiseRole's permissions (see hasFranchisePermission()). Returns a
     * real query builder — chain ->where()/->with()/->get() etc. on it
     * exactly like the old franchiseBookings() relation.
     */
    public function accessibleFranchiseBookingsQuery()
    {
        $memberBookingIds = $this->franchiseTeamMemberships()->pluck('franchise_booking_id');

        return FranchiseBooking::where('user_id', $this->id)
            ->when($memberBookingIds->isNotEmpty(), fn ($q) => $q->orWhereIn('id', $memberBookingIds));
    }

    public function ownsFranchiseBooking(int $franchiseBookingId): bool
    {
        return $this->franchiseBookings()->where('id', $franchiseBookingId)->exists();
    }

    public function hasFranchisePermission(int $franchiseBookingId, string $permission): bool
    {
        if ($this->ownsFranchiseBooking($franchiseBookingId)) {
            return true;
        }

        $membership = $this->franchiseTeamMemberships()
            ->where('franchise_booking_id', $franchiseBookingId)
            ->with('role')
            ->first();

        return $membership?->role?->hasPermission($permission) ?? false;
    }

    /**
     * True if this permission applies to at least one franchise booking the
     * user can reach — used to decide whether a sidebar link is worth
     * showing at all, without pinning it to one specific booking.
     */
    public function hasAnyFranchisePermission(string $permission): bool
    {
        if ($this->franchiseBookings()->exists()) {
            return true;
        }

        return $this->franchiseTeamMemberships()
            ->whereHas('role', fn ($q) => $q->whereJsonContains('permissions', $permission))
            ->exists();
    }

    public function wishlist()
    {
        return $this->belongsToMany(Course::class, 'wishlist')->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function videoProgress()
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function certificateApplications()
    {
        return $this->hasMany(CertificateApplication::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    public function ensureReferralCode(): string
    {
        if (!$this->referral_code) {
            do {
                $code = 'REF' . strtoupper(Str::random(6));
            } while (self::where('referral_code', $code)->exists());

            $this->forceFill(['referral_code' => $code])->save();
        }

        return $this->referral_code;
    }
}
