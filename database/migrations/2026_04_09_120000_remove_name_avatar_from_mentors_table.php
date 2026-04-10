<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            if (Schema::hasColumn('mentors', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('mentors', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'name')) {
                $table->string('name')->after('user_id');
            }
            if (! Schema::hasColumn('mentors', 'avatar')) {
                $table->string('avatar')->nullable()->after('name');
            }
        });
    }
};
