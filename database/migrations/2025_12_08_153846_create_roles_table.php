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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Spatie required
            $table->string('guard_name')->default('web'); // Spatie required
            $table->string('slug')->unique(); // Custom field
            $table->text('description')->nullable(); // Custom field
            $table->timestamps();

            $table->unique(['name', 'guard_name']); // Spatie required unique constraint
            $table->index('slug');
        });

        // Create model_has_roles table (polymorphic)
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->morphs('model'); // Creates model_id and model_type columns
            $table->timestamps();

            $table->unique(['role_id', 'model_id', 'model_type'], 'model_has_roles_unique');
        });

        // Create role_has_permissions table (many-to-many)
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
    }
};
