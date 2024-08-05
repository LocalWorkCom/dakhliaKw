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
        Schema::table('points', function (Blueprint $table) {
            $table->time('from')->nullable(); // Add 'from' column of type TIME
            $table->time('to')->nullable();   // Add 'to' column of type TIME
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn(['from', 'to']); // Remove the columns if rolling back
        });
    }
};
