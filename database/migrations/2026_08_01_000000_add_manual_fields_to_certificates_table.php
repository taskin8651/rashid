<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('course_id');
            $table->string('student_email')->nullable()->after('student_name');
            $table->string('student_phone')->nullable()->after('student_email');
            $table->string('course_name')->nullable()->after('student_phone');
            $table->string('course_duration_text')->nullable()->after('course_name');
            $table->string('source')->default('system')->after('status');
        });

        DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_user_id_foreign');
        DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_course_id_foreign');
        DB::statement('ALTER TABLE certificates DROP INDEX certificates_user_id_course_id_unique');
        DB::statement('ALTER TABLE certificates MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE certificates MODIFY course_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_course_id_foreign FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_user_id_foreign');
        DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_course_id_foreign');
        DB::statement('ALTER TABLE certificates MODIFY user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE certificates MODIFY course_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_course_id_foreign FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE certificates ADD UNIQUE certificates_user_id_course_id_unique (user_id, course_id)');

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'student_name',
                'student_email',
                'student_phone',
                'course_name',
                'course_duration_text',
                'source',
            ]);
        });
    }
};
