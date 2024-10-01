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
        Schema::create('absence_violation', function (Blueprint $table) {
            $table->id();
            $table->integer('actual_number');
            $table->unsignedBigInteger('absence_id')->nullable();
            $table->foreign('absence_id')->references('id')->on('absences');
            $table->unsignedBigInteger('violation_type_id')->nullable();
            $table->foreign('violation_type_id')->references('id')->on('violation_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_violation');
    }
};
