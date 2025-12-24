<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('track_id')->nullable()->after('id')->constrained('tracks')->onDelete('set null');
            $table->enum('course_type', ['course', 'recurring'])->default('course')->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn(['track_id', 'course_type']);
        });
    }
};

