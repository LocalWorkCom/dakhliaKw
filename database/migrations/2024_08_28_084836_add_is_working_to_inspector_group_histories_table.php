<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsWorkingToInspectorGroupHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_group_histories', function (Blueprint $table) {
            $table->boolean('is_working')->default(1);
            $table->unsignedBigInteger('group_team_id')->nullable()->change();
            $table->unsignedBigInteger('group_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspector_group_histories', function (Blueprint $table) {
            $table->dropColumn('is_working');
        });
    }
}
