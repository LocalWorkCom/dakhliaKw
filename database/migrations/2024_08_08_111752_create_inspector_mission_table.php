<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectorMissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspector_mission', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('inspector_id');
            $table->foreign('inspector_id')->references('id')->on('inspectors');


            $table->json('ids_group_point');

            $table->json('ids_instant_mission');

            $table->unsignedBigInteger('working_time_id');
            $table->foreign('working_time_id')->references('id')->on('working_times');

            $table->unsignedBigInteger('working_tree_id');
            $table->foreign('working_tree_id')->references('id')->on('working_trees');

            $table->unsignedBigInteger('vacation_id');
            $table->foreign('vacation_id')->references('id')->on('employee_vacations');

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');

            $table->unsignedBigInteger('group_team_id');
            $table->foreign('group_team_id')->references('id')->on('group_teams');

            $table->date('date');

            $table->boolean('flag')->default(0);

            $table->timestamps();

            // Optionally, you can add foreign key constraints if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspector_mission');
    }
}
