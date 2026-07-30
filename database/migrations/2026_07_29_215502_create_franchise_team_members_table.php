<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['franchise_booking_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_team_members');
    }
};
