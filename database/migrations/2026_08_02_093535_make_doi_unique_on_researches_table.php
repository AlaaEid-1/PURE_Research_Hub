<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, safely handle any duplicate DOIs by keeping the newest one
        // and setting the older duplicates to NULL
        $duplicates = DB::table('researches')
            ->whereNotNull('doi')
            ->select('doi')
            ->groupBy('doi')
            ->havingRaw('COUNT(doi) > 1')
            ->pluck('doi');

        foreach ($duplicates as $doi) {
            $latestId = DB::table('researches')
                ->where('doi', $doi)
                ->orderByDesc('id')
                ->value('id');

            DB::table('researches')
                ->where('doi', $doi)
                ->where('id', '!=', $latestId)
                ->update(['doi' => null]);
        }

        Schema::table('researches', function (Blueprint $table) {
            $table->string('doi')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropUnique(['doi']);
        });
    }
};
