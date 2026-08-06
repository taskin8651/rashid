<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'attendance_location_id', 'date', 'marked_at',
        'method', 'latitude', 'longitude', 'distance_meters', 'accuracy_meters', 'wifi_ssid',
        'device', 'ip_address',
        'check_out_at', 'check_out_method', 'check_out_latitude', 'check_out_longitude',
        'check_out_distance_meters', 'check_out_accuracy_meters', 'check_out_wifi_ssid',
        'check_out_device', 'check_out_ip_address',
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

    public function deviceLabel(): ?string
    {
        return self::parseDeviceLabel($this->device);
    }

    public function checkOutDeviceLabel(): ?string
    {
        return self::parseDeviceLabel($this->check_out_device);
    }

    protected static function parseDeviceLabel(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $platform = match (true) {
            str_contains($userAgent, 'iPhone') => 'iPhone',
            str_contains($userAgent, 'iPad') => 'iPad',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') => 'Mac',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown',
        };

        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'OPR/') => 'Opera',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'CriOS/') => 'Chrome',
            str_contains($userAgent, 'FxiOS/') => 'Firefox',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Browser',
        };

        return "{$platform} · {$browser}";
    }

    public function mapUrl(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function checkOutMapUrl(): ?string
    {
        if ($this->check_out_latitude === null || $this->check_out_longitude === null) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->check_out_latitude},{$this->check_out_longitude}";
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
        return self::formatMinutes($this->durationMinutes());
    }

    public static function formatMinutes(?int $minutes): ?string
    {
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
