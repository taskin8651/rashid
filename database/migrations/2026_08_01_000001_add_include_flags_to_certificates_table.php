<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->boolean('include_certificate')->default(true)->after('source');
            $table->boolean('include_marksheet')->default(true)->after('include_certificate');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['include_certificate', 'include_marksheet']);
        });
    }
};
