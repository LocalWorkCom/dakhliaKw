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
        Schema::create('group_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // We'll store the points_ids as a JSON array
            $table->json('points_ids');
            // Assuming governments table has an id column
            $table->unsignedBigInteger('government_id');
            $table->foreign('government_id')->references('id')->on('governments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_points');
    }
};
