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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
            $table->enum('type', ['video', 'document', 'mixed'])->default('video');
            $table->string('title');
            $table->string('video')->nullable();
            $table->string('attached_file')->nullable();
            $table->text('attached_text')->nullable();
            $table->string('document')->nullable();
            $table->unsignedInteger('order_by')->default(0);
            $table->string('durations')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
