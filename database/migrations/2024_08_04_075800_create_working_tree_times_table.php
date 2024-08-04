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
        Schema::create('working_tree_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('working_tree_id');
            $table->unsignedBigInteger('working_time_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('created_department');

            // Foreign keys
            $table->foreign('working_tree_id')->references('id')->on('working_trees')->onUpdate('cascade');
            $table->foreign('working_time_id')->references('id')->on('working_times')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('created_department')->references('id')->on('departments')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_tree_times');
    }
};
