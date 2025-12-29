<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change live_meeting_link from VARCHAR(255) to TEXT to support long URLs with config parameters
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Change column type from string (VARCHAR 255) to text to support long URLs
            $table->text('live_meeting_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Revert back to string (VARCHAR 255)
            $table->string('live_meeting_link')->nullable()->change();
        });
    }
};
