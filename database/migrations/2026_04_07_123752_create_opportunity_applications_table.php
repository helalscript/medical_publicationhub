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
        Schema::create('opportunity_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('highest_edu_level')->nullable();
            $table->string('institution')->nullable();
            $table->string('field_of_study')->nullable();
            $table->text('research_experience')->nullable();
            $table->string('current_position')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('personal_website')->nullable();
            $table->json('documents')->nullable();
            $table->longText('cover_letter')->nullable();
            $table->boolean('accept_term')->default(false);
            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunity_applications');
    }
};
