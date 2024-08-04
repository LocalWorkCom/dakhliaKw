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
            $table->unsignedBigInteger('working_tree_id')->nullable();
            $table->unsignedBigInteger('working_time_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('created_department')->nullable();

            // Foreign keys
            $table->foreign('working_tree_id')->references('id')->on('working_trees');
            $table->foreign('working_time_id')->references('id')->on('working_times');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('created_department')->references('id')->on('departments');

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
