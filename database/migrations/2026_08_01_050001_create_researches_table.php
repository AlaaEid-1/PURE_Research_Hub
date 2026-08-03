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
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('research_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract');
            $table->text('keywords')->nullable();
            $table->string('doi')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('pdf_path');
            $table->string('thumbnail_path')->nullable();
            $table->text('copyright_information')->nullable();
            $table->string('download_permission')->default('free');
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
