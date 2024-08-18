<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('inspector_mission', function (Blueprint $table) {
            $table->json('personal_mission_ids')->nullable();// Adjust 'existing_column' if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('inspector_mission', function (Blueprint $table) {
            $table->dropColumn('personal_mission_ids');
        });
    }
};
