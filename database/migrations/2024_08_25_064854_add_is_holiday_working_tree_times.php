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
        if (!Schema::hasColumn('working_tree_times', 'is_holiday')) {

            Schema::table('working_tree_times', function (Blueprint $table) {
                $table->boolean('is_holiday')->default(0);
            });
        }
        if (Schema::hasColumn('working_tree_times', 'working_time_id')) {
            Schema::table('working_tree_times', function (Blueprint $table) {
                $table->integer('working_time_id')->nullable()->change();
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
