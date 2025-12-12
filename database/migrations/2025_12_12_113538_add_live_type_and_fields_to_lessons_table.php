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
        // Add 'live' to the enum type
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('text', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame', 'assignment', 'test', 'live') DEFAULT 'text'");
        
        // Add live meeting fields
        Schema::table('lessons', function (Blueprint $table) {
            $table->dateTime('live_meeting_date')->nullable()->after('video_url');
            $table->string('live_meeting_link')->nullable()->after('live_meeting_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['live_meeting_date', 'live_meeting_link']);
        });
        
        // Remove 'live' from the enum type
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('text', 'google_drive_video', 'video_file', 'youtube_video', 'image', 'document_file', 'embed_frame', 'assignment', 'test') DEFAULT 'text'");
    }
};
