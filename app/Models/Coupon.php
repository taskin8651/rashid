<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_amount', 'valid_until', 'usage_limit', 'used_count', 'status',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }
        return true;
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
