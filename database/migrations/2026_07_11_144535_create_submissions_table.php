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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_assignment_id');
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->text('content_text')->nullable(); // For extracted text or direct text input
            $table->dateTime('submitted_at');
            $table->timestamps();
            
            $table->foreign('task_assignment_id')->references('id')->on('task_assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
