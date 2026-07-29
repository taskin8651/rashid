<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'attendance_location_id', 'date', 'marked_at',
        'method', 'latitude', 'longitude', 'distance_meters', 'wifi_ssid',
        'check_out_at', 'check_out_method', 'check_out_latitude', 'check_out_longitude',
        'check_out_distance_meters', 'check_out_wifi_ssid',
    ];

    protected $casts = [
        'date' => 'date',
        'marked_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'check_out_at' => 'datetime',
        'check_out_latitude' => 'decimal:7',
        'check_out_longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(AttendanceLocation::class, 'attendance_location_id');
    }

    public function isPunchedOut(): bool
    {
        return $this->check_out_at !== null;
    }

    public function durationMinutes(): ?int
    {
        if (!$this->check_out_at) {
            return null;
        }

        return $this->marked_at->diffInMinutes($this->check_out_at);
    }

    public function durationLabel(): ?string
    {
        $minutes = $this->durationMinutes();

        if ($minutes === null) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }

        return "{$mins}m";
    }
}
