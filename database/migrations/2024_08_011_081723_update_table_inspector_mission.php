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
        Schema::table('inspector_mission', function (Blueprint $table) {
            $table->json('ids_group_point')->nullable()->change(); // Allow null values
            $table->json('ids_instant_mission')->nullable()->change(); // Allow null values

            $table->unsignedBigInteger('working_time_id')->nullable()->change(); // Allow null values

            $table->unsignedBigInteger('vacation_id')->nullable()->change(); // Allow null values
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspector_mission', function (Blueprint $table) {
            $table->dropForeign(['working_time_id']); // Drop the foreign key constraint
            $table->dropColumn('working_time_id'); // Remove the column

            $table->dropForeign(['vacation_id']); // Drop the foreign key constraint
            $table->dropColumn('vacation_id'); // Remove the column

            $table->dropColumn('ids_group_point'); // Remove the column
            $table->dropColumn('ids_instant_mission'); // Remove the column
        });
    }
};
