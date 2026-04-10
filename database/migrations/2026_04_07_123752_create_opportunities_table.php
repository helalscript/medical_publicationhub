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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
            $table->json('category_ids')->nullable();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->longText('overview')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('desciptions')->nullable();
            $table->date('dead_line')->nullable();
            $table->string('durations')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('speciality_ids')->nullable();
            $table->json('studydesign_type_ids')->nullable();
            $table->json('contribution_type_ids')->nullable();
            $table->json('university_hospital_ids')->nullable();
            $table->json('place_ids')->nullable();
            $table->json('experience_level_ids')->nullable();
            $table->boolean('is_future')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
