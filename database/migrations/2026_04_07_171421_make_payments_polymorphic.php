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
        $hasPayable = Schema::hasColumn('payments', 'payable_type') && Schema::hasColumn('payments', 'payable_id');
        $hasCourseId = Schema::hasColumn('payments', 'course_id');

        if (! $hasPayable) {
            Schema::table('payments', function (Blueprint $table) {
                // polymorphic target (Course / Opportunity / etc)
                $table->nullableMorphs('payable');
            });
        }

        if ($hasCourseId) {
            Schema::table('payments', function (Blueprint $table) {
                // drop old course_id column (course becomes payable)
                $table->dropColumn('course_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasPayable = Schema::hasColumn('payments', 'payable_type') && Schema::hasColumn('payments', 'payable_id');
        $hasCourseId = Schema::hasColumn('payments', 'course_id');

        if ($hasPayable) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropMorphs('payable');
            });
        }

        if (! $hasCourseId) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('user_id');
                $table->index(['user_id', 'course_id']);
            });
        }
    }
};
