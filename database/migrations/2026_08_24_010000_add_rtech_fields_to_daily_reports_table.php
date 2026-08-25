<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('user_id')->constrained('courses')->nullOnDelete();

            $table->text('topics_covered')->nullable()->after('description');
            $table->unsignedInteger('students_present')->nullable()->after('topics_covered');
            $table->boolean('homework_assigned')->default(false)->after('students_present');

            $table->unsignedInteger('leads_followed_up')->nullable()->after('homework_assigned');
            $table->unsignedInteger('students_enrolled')->nullable()->after('leads_followed_up');
            $table->unsignedInteger('calls_made')->nullable()->after('students_enrolled');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn([
                'course_id', 'topics_covered', 'students_present', 'homework_assigned',
                'leads_followed_up', 'students_enrolled', 'calls_made',
            ]);
        });
    }
};
