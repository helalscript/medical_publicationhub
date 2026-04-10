<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('last_seen_message_id')->nullable();
            $table->enum('role', ['mentor', 'researcher', 'manager', 'coordinator', 'super_admin'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['chat_id', 'user_id'], 'chat_users_unique_chat_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_users');
    }
};

