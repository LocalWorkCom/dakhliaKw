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
            $table->foreignId('working_tree_id')->nullable()->references('id')->on('working_trees');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_teams', function (Blueprint $table) {
            
            
            $table->dropColumn('working_tree_id');
        });
    }
};
