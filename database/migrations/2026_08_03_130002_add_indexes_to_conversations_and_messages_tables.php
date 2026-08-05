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
        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['author_id', 'last_message_at'], 'conversations_author_last_message_idx');
            $table->index(['sender_id', 'last_message_at'], 'conversations_sender_last_message_idx');
            $table->index('last_message_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'created_at'], 'messages_conversation_created_idx');
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('conversations_author_last_message_idx');
            $table->dropIndex('conversations_sender_last_message_idx');
            $table->dropIndex(['last_message_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conversation_created_idx');
            $table->dropIndex(['read_at']);
        });
    }
};
