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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('calendly_personal_access_token')->nullable()->after('web_url');
            $table->string('calendly_user_uri')->nullable()->after('calendly_personal_access_token');
            $table->string('calendly_event_type_uri')->nullable()->after('calendly_user_uri');
            $table->string('calendly_scheduling_url')->nullable()->after('calendly_event_type_uri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'calendly_personal_access_token',
                'calendly_user_uri',
                'calendly_event_type_uri',
                'calendly_scheduling_url',
            ]);
        });
    }
};
