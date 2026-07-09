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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('schedule_text');
            $table->date('start_date');
            $table->enum('mode', ['online', 'offline'])->default('online');
            $table->unsignedInteger('seats_total')->nullable();
            $table->unsignedInteger('seats_filled')->default(0);
            $table->enum('status', ['upcoming', 'ongoing', 'closed'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
