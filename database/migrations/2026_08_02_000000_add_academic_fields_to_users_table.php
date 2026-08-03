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
        Schema::table('users', function (Blueprint $table) {
            $table->text('research_interests')->nullable()->after('bio');
            $table->string('orcid_id')->nullable()->after('research_interests');
            $table->string('google_scholar_url')->nullable()->after('orcid_id');
            $table->string('website_url')->nullable()->after('google_scholar_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'research_interests',
                'orcid_id',
                'google_scholar_url',
                'website_url',
            ]);
        });
    }
};
