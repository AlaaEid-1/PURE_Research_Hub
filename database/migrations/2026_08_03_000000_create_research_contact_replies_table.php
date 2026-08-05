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
        Schema::table('research_contact_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('research_contact_requests', 'subject')) {
                $table->string('subject')->nullable()->after('sender_id');
            }
        });

        Schema::create('research_contact_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_request_id')->constrained('research_contact_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_contact_replies');

        Schema::table('research_contact_requests', function (Blueprint $table) {
            if (Schema::hasColumn('research_contact_requests', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};
