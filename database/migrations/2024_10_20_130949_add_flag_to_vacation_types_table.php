<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagToVacationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacation_types', function (Blueprint $table) {
            $table->boolean('flag')
                ->default(1)
                ->comment('1 will view in app, 0 not view');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacation_types', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
}
