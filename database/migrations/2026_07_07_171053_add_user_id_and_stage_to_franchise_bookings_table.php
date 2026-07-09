<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('franchise_bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('franchise_lead_id')->constrained('users')->nullOnDelete();
            $table->enum('stage', ['registered', 'contacted', 'site_visit', 'agreement', 'training', 'launched'])
                ->default('registered')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franchise_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('stage');
        });
    }
};
