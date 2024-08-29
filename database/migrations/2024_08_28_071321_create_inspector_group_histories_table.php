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
    Schema::create('inspector_group_histories', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('group_id');
        $table->unsignedBigInteger('group_team_id');
        $table->unsignedBigInteger('inspector_id');
        $table->date('date');
        $table->timestamps();

        // Add foreign key constraints if necessary
        $table->foreign('group_id')->references('id')->on('groups');
        $table->foreign('group_team_id')->references('id')->on('group_teams');
        $table->foreign('inspector_id')->references('id')->on('inspectors');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspector_group_histories');
    }
};
