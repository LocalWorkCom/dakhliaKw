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
        //
        Schema::table('group_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('inspector_manager')->nullable(); // Adding inspector_manager column
            $table->foreign('inspector_manager')->references('id')->on('inspectors'); // Setting up foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
