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
        Schema::create('personal_missions', function (Blueprint $table) {
            $table->id(); // Auto-increment column for ID
            $table->date('date');
            $table->unsignedBigInteger('point_id'); // Foreign key for group_points
            $table->unsignedBigInteger('inspector_id'); // Foreign key for inspector
            $table->unsignedBigInteger('group_id'); // Foreign key for groups
            $table->unsignedBigInteger('team_id'); // Foreign key for group_team

            // Define foreign key constraints
            $table->foreign('inspector_id')->references('id')->on('inspectors');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('team_id')->references('id')->on('group_teams');
            $table->foreign('point_id')->references('id')->on('group_points');

            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_missions');
    }
};
