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
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_code')->nullable()->unique()->after('id');
            $table->string('guardian_name')->nullable()->after('date_of_birth');
            $table->string('blood_group', 5)->nullable()->after('guardian_name');
            $table->string('emergency_contact', 20)->nullable()->after('blood_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_code', 'guardian_name', 'blood_group', 'emergency_contact']);
        });
    }
};
