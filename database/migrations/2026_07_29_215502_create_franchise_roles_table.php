<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_booking_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('permissions');
            $table->timestamps();

            $table->unique(['franchise_booking_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_roles');
    }
};
