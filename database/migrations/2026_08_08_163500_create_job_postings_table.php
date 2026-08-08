<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('company_name')->default('R-Tech Computer');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('job_type')->nullable();
            $table->string('work_mode')->nullable();
            $table->string('location')->nullable();
            $table->string('package')->nullable();
            $table->unsignedInteger('vacancies')->nullable();
            $table->date('apply_by')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
