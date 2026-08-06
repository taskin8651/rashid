<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttendanceLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'franchise_booking_id', 'latitude', 'longitude',
        'radius_meters', 'wifi_ssid', 'qr_token', 'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (AttendanceLocation $location) {
            if (!$location->qr_token) {
                $location->qr_token = (string) Str::uuid();
            }
        });
    }

    public function franchiseBooking()
    {
        return $this->belongsTo(FranchiseBooking::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function ownerLabel(): string
    {
        return $this->franchise_booking_id
            ? '🏙 ' . $this->franchiseBooking->city . ' Franchise'
            : 'R-Tech Official HQ';
    }

    public function scanUrl(): string
    {
        return route('attendance.scan', $this->qr_token);
    }

    /**
     * Great-circle (haversine) distance in meters between this location and
     * a given point — used to enforce the GPS geofence at check-in time.
     */
    public function distanceTo(float $lat, float $lng): ?int
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        $earthRadius = 6371000; // meters

        $latFrom = deg2rad((float) $this->latitude);
        $lngFrom = deg2rad((float) $this->longitude);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * $accuracyBuffer extends the geofence by the phone's own reported GPS
     * accuracy (capped by the caller) — a hard cutoff at radius_meters with
     * no tolerance was rejecting genuine students whenever their phone's GPS
     * fix simply wasn't precise, which was disproportionately common at
     * check-out time (rushed, often further from the anchor point).
     */
    public function isWithinRadius(float $lat, float $lng, float $accuracyBuffer = 0): bool
    {
        $distance = $this->distanceTo($lat, $lng);

        return $distance !== null && $distance <= ($this->radius_meters + $accuracyBuffer);
    }

    public function matchesWifi(string $ssid): bool
    {
        return $this->wifi_ssid && strcasecmp(trim($this->wifi_ssid), trim($ssid)) === 0;
    }
}
