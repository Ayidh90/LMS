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
        // Add Arabic fields to roles table
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'name_ar')) {
                    $table->string('name_ar')->nullable()->after('name');
                }
                if (!Schema::hasColumn('roles', 'description_ar')) {
                    $table->text('description_ar')->nullable()->after('description');
                }
            });
        }

        // Add Arabic fields to permissions table
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'name_ar')) {
                    $table->string('name_ar')->nullable()->after('name');
                }
                if (!Schema::hasColumn('permissions', 'description_ar')) {
                    $table->text('description_ar')->nullable()->after('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Arabic fields from roles table
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (Schema::hasColumn('roles', 'name_ar')) {
                    $table->dropColumn('name_ar');
                }
                if (Schema::hasColumn('roles', 'description_ar')) {
                    $table->dropColumn('description_ar');
                }
            });
        }

        // Remove Arabic fields from permissions table
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (Schema::hasColumn('permissions', 'name_ar')) {
                    $table->dropColumn('name_ar');
                }
                if (Schema::hasColumn('permissions', 'description_ar')) {
                    $table->dropColumn('description_ar');
                }
            });
        }
    }
};
