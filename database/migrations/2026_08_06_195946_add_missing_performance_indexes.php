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
        Schema::table('research_access_requests', function (Blueprint $table) {
            $table->index('research_id');
            $table->index('requester_id');
            $table->index('status');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('conversation_id');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index('research_id');
        });

        Schema::table('researches', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_access_requests', function (Blueprint $table) {
            $table->dropIndex(['research_id']);
            $table->dropIndex(['requester_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['research_id']);
        });

        Schema::table('researches', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
        });
    }
};
