<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('franchise_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('source', ['walk_in', 'phone', 'website', 'referral', 'social_media', 'other'])->default('walk_in');
            $table->enum('status', ['new', 'contacted', 'follow_up', 'interested', 'converted', 'lost'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('next_follow_up_date')->nullable();
            $table->string('lost_reason')->nullable();
            $table->foreignId('converted_enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_leads');
    }
};
