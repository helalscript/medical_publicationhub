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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->json('category_ids')->nullable();
            $table->string('title');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->string('short_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_description')->nullable();
            $table->string('thumbnail_button_text')->nullable();
            $table->string('intro_video')->nullable();
            $table->text('overview')->nullable();
            $table->json('descriptions')->nullable();
            $table->json('mentor_ids')->nullable();
            $table->boolean('is_future')->default(false);
            $table->string('total_durations')->nullable();
            $table->unsignedInteger('start_module')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
