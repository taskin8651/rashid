<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('device')->nullable()->after('wifi_ssid');
            $table->string('ip_address')->nullable()->after('device');
            $table->string('check_out_device')->nullable()->after('check_out_wifi_ssid');
            $table->string('check_out_ip_address')->nullable()->after('check_out_device');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['device', 'ip_address', 'check_out_device', 'check_out_ip_address']);
        });
    }
};
