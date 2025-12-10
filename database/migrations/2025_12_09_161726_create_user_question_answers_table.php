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
        Schema::create('user_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('answer_id')->nullable()->constrained('answers')->onDelete('cascade'); // For multiple choice/true-false
            $table->text('answer_text')->nullable(); // For short_answer/essay
            $table->boolean('is_correct')->default(false);
            $table->integer('points_earned')->default(0);
            $table->timestamps();
            
            // Unique constraint: user can only answer a question once
            $table->unique(['user_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_question_answers');
    }
};
