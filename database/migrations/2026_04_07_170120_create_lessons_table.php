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
        // If a previous failed migration attempt created the table, drop it first.
        Schema::dropIfExists('lessons');

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            // NOTE: modules migration runs after lessons (filename sort),
            // so we add the FK in a follow-up migration.
            $table->unsignedBigInteger('module_id');
            $table->string('title')->nullable();
            $table->string('file')->nullable();
            $table->string('video')->nullable();
            $table->text('attached_text')->nullable();
            $table->string('attached_file')->nullable();
            $table->string('document')->nullable();
            $table->unsignedInteger('order_by')->default(0);
            $table->string('durations')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
