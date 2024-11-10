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
        Schema::table('group_teams', function (Blueprint $table) {
            //
            $table->boolean('changed')->default(0); // Replace 'column_name' with the name of the column after which you want to add 'changed'

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_teams', function (Blueprint $table) {
            //
            $table->dropColumn('changed');
        });
    }
};
