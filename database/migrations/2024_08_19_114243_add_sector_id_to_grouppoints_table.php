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
        Schema::table('group_points', function (Blueprint $table) {
            $table->integer('deleted')->nullable()->default(0)->comment('1=>point has transfer to group , 0=>point has not assigned to group');
            $table->unsignedBigInteger('sector_id')->nullable(); // Adding sector_id column
            $table->foreign('sector_id')->references('id')->on('sectors'); // Setting up foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_points', function (Blueprint $table) {
            $table->dropForeign(['sector_id']); // Dropping the foreign key
            $table->dropColumn('sector_id'); // Dropping the column
            $table->dropColumn('deleted'); // Dropping the column

        });
    }
};
