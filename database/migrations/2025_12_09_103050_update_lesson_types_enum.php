<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, temporarily expand the enum to include both old and new values
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video', 'text', 'quiz', 'assignment', 'live', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame') DEFAULT 'text'");
        
        // Then, update existing data to match new enum values
        // Map old values to new values
        DB::statement("UPDATE lessons SET type = 'youtube_video' WHERE type = 'video'");
        DB::statement("UPDATE lessons SET type = 'text' WHERE type = 'quiz'");
        DB::statement("UPDATE lessons SET type = 'text' WHERE type = 'assignment'");
        DB::statement("UPDATE lessons SET type = 'youtube_video' WHERE type = 'live'");
        
        // Now change the enum type to only include new values
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('text', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum types
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video', 'text', 'quiz', 'assignment', 'live') DEFAULT 'video'");
    }
};
