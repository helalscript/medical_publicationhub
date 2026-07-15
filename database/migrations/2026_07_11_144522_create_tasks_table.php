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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mentor_id');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('rubric_id')->nullable();
            $table->dateTime('deadline');
            $table->string('video_url')->nullable();
            $table->timestamps();
            
            $table->foreign('mentor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rubric_id')->references('id')->on('rubrics')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
