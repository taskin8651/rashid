<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('check_out_at')->nullable()->after('marked_at');
            $table->enum('check_out_method', ['gps', 'wifi'])->nullable()->after('check_out_at');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_method');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            $table->unsignedInteger('check_out_distance_meters')->nullable()->after('check_out_longitude');
            $table->string('check_out_wifi_ssid')->nullable()->after('check_out_distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_out_at', 'check_out_method', 'check_out_latitude',
                'check_out_longitude', 'check_out_distance_meters', 'check_out_wifi_ssid',
            ]);
        });
    }
};
