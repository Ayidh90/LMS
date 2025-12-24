<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_progress_id')->constrained('track_progress')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->onDelete('set null');
            $table->integer('progress')->default(0); // Percentage 0-100
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['track_progress_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_course_progress');
    }
};

