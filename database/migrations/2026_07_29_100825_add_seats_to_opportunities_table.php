<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Total capacity for this opportunity (null = unlimited)
            $table->unsignedInteger('total_seats')->nullable()->after('price');
            // Remaining seats (decremented on each enrollment)
            $table->unsignedInteger('available_seats')->nullable()->after('total_seats');
        });
    }

    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropColumn(['total_seats', 'available_seats']);
        });
    }
};
