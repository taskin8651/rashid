<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedInteger('accuracy_meters')->nullable()->after('distance_meters');
            $table->unsignedInteger('check_out_accuracy_meters')->nullable()->after('check_out_distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['accuracy_meters', 'check_out_accuracy_meters']);
        });
    }
};
