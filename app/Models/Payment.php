<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type', 'payable_id', 'user_id',
        'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'razorpay_refund_id',
        'amount', 'currency', 'method', 'note', 'recorded_by', 'paid_at', 'status', 'refunded_at', 'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
        'refunded_at' => 'datetime',
        'paid_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function payable()
    {
        return $this->payable_type === 'course_enrollment'
            ? Enrollment::find($this->payable_id)
            : FranchiseBooking::find($this->payable_id);
    }
}
