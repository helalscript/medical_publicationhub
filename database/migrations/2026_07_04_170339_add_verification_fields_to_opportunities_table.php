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
        Schema::table('opportunities', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->date('verification_deadline')->nullable();
            $table->unsignedBigInteger('coordinator_id')->nullable();

            $table->foreign('coordinator_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['coordinator_id']);
            $table->dropColumn(['is_verified', 'verification_deadline', 'coordinator_id']);
        });
    }
};
