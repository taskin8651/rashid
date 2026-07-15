<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reward_coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->enum('status', ['pending', 'rewarded'])->default('pending');
            $table->timestamps();
            $table->unique('referred_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
