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
        Schema::create('ai_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->json('rubric_scores')->nullable(); // detailed scores
            $table->json('feedback_comments')->nullable(); // AI comments per rubric item
            $table->text('general_feedback')->nullable();
            $table->json('flagged_issues')->nullable();
            $table->text('edited_excerpt')->nullable();
            $table->decimal('mentor_score_override', 5, 2)->nullable();
            $table->text('mentor_comments_override')->nullable();
            $table->string('status')->default('pending_mentor'); // pending_mentor, approved, rejected
            $table->unsignedBigInteger('mentor_id')->nullable(); // Who reviewed it
            $table->timestamps();
            
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->foreign('mentor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feedback');
    }
};
