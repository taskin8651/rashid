<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('franchise_bookings', function (Blueprint $table) {
            $table->string('lead_form_token')->nullable()->unique()->after('stage');
        });
    }

    public function down(): void
    {
        Schema::table('franchise_bookings', function (Blueprint $table) {
            $table->dropColumn('lead_form_token');
        });
    }
};
