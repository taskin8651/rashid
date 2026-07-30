<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('note')->nullable()->after('method');
            $table->foreignId('recorded_by')->nullable()->after('note')->constrained('users')->nullOnDelete();
            $table->date('paid_at')->nullable()->after('recorded_by');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn(['note', 'paid_at']);
        });
    }
};
