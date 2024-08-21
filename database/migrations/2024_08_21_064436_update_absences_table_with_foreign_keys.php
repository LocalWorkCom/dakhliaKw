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
        Schema::table('absences', function (Blueprint $table) {
            // Ensure the columns exist and add foreign key constraints
            $table->unsignedBigInteger('point_id')->change();
            $table->unsignedBigInteger('mission_id')->change();
            $table->unsignedBigInteger('inspector_id')->change();

            // Add the foreign key constraints
            $table->foreign('point_id')->nullable()->references('id')->on('points');
            $table->foreign('mission_id')->nullable()->references('id')->on('inspector_mission');
            $table->foreign('inspector_id')->nullable()->references('id')->on('inspectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            // Drop the foreign key constraints
            $table->dropForeign(['point_id']);
            $table->dropForeign(['mission_id']);
            $table->dropForeign(['inspector_id']);
        });
    }
};
