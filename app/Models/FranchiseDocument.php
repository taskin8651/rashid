<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseDocument extends Model
{
    public const TYPES = [
        'agreement' => 'Franchise Agreement',
        'id_proof' => 'ID Proof',
        'space_photo' => 'Space Photo',
        'other' => 'Other',
    ];

    protected $fillable = ['franchise_booking_id', 'uploaded_by', 'type', 'label', 'file_path', 'original_name'];

    public function booking()
    {
        return $this->belongsTo(FranchiseBooking::class, 'franchise_booking_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
