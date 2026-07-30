<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseRole extends Model
{
    use HasFactory;

    /**
     * Permissions a franchise owner can grant to their own team members.
     * Scoped entirely to that franchise's own data — unlike the admin
     * permission list, these aren't Spatie permissions since each
     * franchise needs its own independent set of roles.
     */
    public const PERMISSIONS = [
        'manage-leads',
        'follow-up-leads',
        'manage-courses',
        'view-students',
        'manage-students',
        'manage-gallery',
        'manage-attendance',
        'manage-documents',
    ];

    protected $fillable = ['franchise_booking_id', 'name', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function teamMembers()
    {
        return $this->hasMany(FranchiseTeamMember::class);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }
}
