<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public const CATEGORIES = ['rent', 'salary', 'marketing', 'utilities', 'equipment', 'maintenance', 'other'];

    protected $fillable = [
        'franchise_booking_id', 'category', 'title', 'amount', 'expense_date',
        'method', 'note', 'receipt_path', 'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
