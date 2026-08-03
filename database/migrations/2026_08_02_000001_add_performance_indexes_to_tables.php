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
        Schema::table('researches', function (Blueprint $table) {
            $table->index(['status', 'publication_date'], 'idx_researches_status_pubdate');
            $table->index(['category_id', 'status'], 'idx_researches_cat_status');
            $table->index(['user_id', 'status'], 'idx_researches_user_status');
            $table->index('views', 'idx_researches_views');
            $table->index('downloads', 'idx_researches_downloads');
        });

        Schema::table('research_access_requests', function (Blueprint $table) {
            $table->index(['research_id', 'status'], 'idx_access_req_research_status');
            $table->index(['requester_id', 'status'], 'idx_access_req_requester_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropIndex('idx_researches_status_pubdate');
            $table->dropIndex('idx_researches_cat_status');
            $table->dropIndex('idx_researches_user_status');
            $table->dropIndex('idx_researches_views');
            $table->dropIndex('idx_researches_downloads');
        });

        Schema::table('research_access_requests', function (Blueprint $table) {
            $table->dropIndex('idx_access_req_research_status');
            $table->dropIndex('idx_access_req_requester_status');
        });
    }
};
