<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseTeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['franchise_booking_id', 'user_id', 'franchise_role_id'];

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(FranchiseRole::class, 'franchise_role_id');
    }
}
