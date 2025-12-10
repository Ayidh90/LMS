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
        // Check if batch_id column doesn't exist before adding it
        if (!Schema::hasColumn('enrollments', 'batch_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->foreignId('batch_id')->nullable()->after('student_id')->constrained('batches')->onDelete('cascade');
            });
        }
        
        // Make course_id nullable (for backward compatibility, can be removed later)
        if (Schema::hasColumn('enrollments', 'course_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->change();
            });
        }
        
        // Update unique constraint to allow multiple enrollments in different batches of same course
        // First, drop ALL foreign keys that might be preventing the unique constraint drop
        try {
            // Get database name
            $dbName = DB::getDatabaseName();
            
            // Get all foreign keys on enrollments table
            $allForeignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = 'enrollments' 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$dbName]);
            
            foreach ($allForeignKeys as $fk) {
                $fkName = $fk->CONSTRAINT_NAME;
                // Check if this FK involves course_id
                $fkColumns = DB::select("
                    SELECT COLUMN_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = ? 
                    AND TABLE_NAME = 'enrollments' 
                    AND CONSTRAINT_NAME = ?
                ", [$dbName, $fkName]);
                
                $involvesCourseId = false;
                foreach ($fkColumns as $col) {
                    if ($col->COLUMN_NAME === 'course_id') {
                        $involvesCourseId = true;
                        break;
                    }
                }
                
                // Drop foreign key if it involves course_id
                if ($involvesCourseId) {
                    DB::statement("ALTER TABLE `enrollments` DROP FOREIGN KEY `{$fkName}`");
                }
            }
        } catch (\Exception $e) {
            // Continue if fails - might not have foreign keys
        }
        
        // Now drop the unique constraint using raw SQL (more reliable)
        try {
            $indexes = DB::select("SHOW INDEXES FROM enrollments WHERE Key_name = 'enrollments_student_id_course_id_unique'");
            if (count($indexes) > 0) {
                DB::statement("ALTER TABLE `enrollments` DROP INDEX `enrollments_student_id_course_id_unique`");
            }
        } catch (\Exception $e) {
            // Index might not exist or still has dependency, skip
        }
        
        // Re-add foreign key on course_id if column exists (for backward compatibility)
        if (Schema::hasColumn('enrollments', 'course_id')) {
            try {
                // Check if foreign key already exists
                $existingFk = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'enrollments' 
                    AND COLUMN_NAME = 'course_id' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                if (count($existingFk) == 0) {
                    Schema::table('enrollments', function (Blueprint $table) {
                        $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                    });
                }
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
        }
        
        // Add unique constraint if it doesn't exist
        $batchIndexes = DB::select("SHOW INDEXES FROM enrollments WHERE Key_name = 'enrollments_student_id_batch_id_unique'");
        if (count($batchIndexes) == 0 && Schema::hasColumn('enrollments', 'batch_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->unique(['student_id', 'batch_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
            $table->foreignId('course_id')->nullable(false)->change();
            $table->dropUnique(['student_id', 'batch_id']);
            $table->unique(['student_id', 'course_id']);
        });
    }
};
