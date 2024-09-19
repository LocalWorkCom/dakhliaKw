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
        Schema::create('paper_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('point_id');
            $table->unsignedBigInteger('mission_id');
            $table->unsignedBigInteger('inspector_id');
            $table->text('civil_number');
            $table->text('registration_number');
            $table->text('images')->nullable();

            $table->integer('status')->comment(' 0 -> is not active , 1->is active');
            $table->integer('parent');

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('point_id')->references('id')->on('points');
            $table->foreign('mission_id')->references('id')->on('inspector_mission');
            $table->foreign('inspector_id')->references('id')->on('inspectors');
            $table->foreign('created_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_transactions');
    }
};
