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
        Schema::table('working_tree_times', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['working_time_id']);
            
            // Modify the column
            $table->integer('working_time_id')->nullable()->change();
            
            // Recreate the foreign key constraint
            $table->foreign('working_time_id')->references('id')->on('working_times');
        });
        
        // Add 'is_holiday' column if it doesn't exist
        if (!Schema::hasColumn('working_tree_times', 'is_holiday')) {
            Schema::table('working_tree_times', function (Blueprint $table) {
                $table->boolean('is_holiday')->default(0);
            });
        }
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    
    }
};
