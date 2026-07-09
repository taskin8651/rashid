<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseLead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'city', 'budget_range', 'message', 'status'];

    public function bookings()
    {
        return $this->hasMany(FranchiseBooking::class);
    }
}
