<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists before dropping
        if (Schema::hasColumn('courses', 'instructor_id')) {
        Schema::table('courses', function (Blueprint $table) {
                // Check if foreign key exists before dropping
                try {
                    $table->dropForeign(['instructor_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('instructor_id');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('cascade');
        });
    }
};
