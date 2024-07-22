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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('job');

            // Add the new 'job_id' column
            $table->unsignedBigInteger('job_id')->nullable()->after('job_title'); // Replace 'some_existing_column' with the name of the column after which 'job_id' should be added

            // Add the foreign key constraint
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
 // Drop the foreign key constraint
 $table->dropForeign(['job_id']);

 // Drop the 'job_id' column
 $table->dropColumn('job_id');

 // Recreate the 'job' column
 $table->string('job')->nullable();
        });
    }
};
