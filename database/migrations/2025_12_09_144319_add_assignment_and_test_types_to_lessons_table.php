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
        // Add assignment and test types to the enum
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('text', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame', 'assignment', 'test') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove assignment and test types
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('text', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame') DEFAULT 'text'");
    }
};
