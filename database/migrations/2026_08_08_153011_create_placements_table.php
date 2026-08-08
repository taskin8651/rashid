<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('franchise_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('job_title');
            $table->string('job_type')->nullable();
            $table->string('work_mode')->nullable();
            $table->string('location')->nullable();
            $table->string('package')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('offer_letter_path')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->text('testimonial')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
