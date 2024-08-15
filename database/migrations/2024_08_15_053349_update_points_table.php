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
        Schema::table('points', function (Blueprint $table) {
            // Drop the `from` and `to` columns
            $table->dropColumn(['from', 'to']);

            // Add the `work_type` column with constraint (only 0 and 1 allowed)
            $table->tinyInteger('work_type')->nullable(false)->comment('0 for 24 hours, 1 for part-time');
            $table->json('days_work')->nullable()->comment('0 => السبت
            ,1 => الأحد
            , 2=> الأثنين
            , 3=> الثلاثاء
            ,4 => الأربعاء
            ,5 => الخميس
            ,6=>الجمعه') ; // Add a JSON column to store an array of strings

            // Add the `created_by` column and set up the foreign key
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
