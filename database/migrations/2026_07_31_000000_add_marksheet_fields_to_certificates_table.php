<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('roll_no')->nullable()->after('cert_code');
            $table->string('father_name')->nullable()->after('roll_no');
            $table->string('batch_name')->nullable()->after('father_name');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['roll_no', 'father_name', 'batch_name']);
        });
    }
};
