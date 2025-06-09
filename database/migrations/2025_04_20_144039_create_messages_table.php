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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->nullable();
            $table->foreignId('chat_id')->constrained('chats');
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->text('context')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'receiver_id']);
        });
        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('last_message_id')
                  ->nullable()
                  ->after('second_user_id')
                  ->constrained('messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['last_message_id']);
            $table->dropColumn('last_message_id');
        });
        Schema::dropIfExists('messages');
    }
};
