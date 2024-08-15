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
        Schema::create('point_days', function (Blueprint $table) {
            $table->id();  // Auto-increment column for ID
            $table->string('name')->comment('0 => السبت
            ,1 => الأحد
            , 2=> الأثنين
            , 3=> الثلاثاء
            ,4 => الأربعاء
            ,5 => الخميس
            ,6=>الجمعه') ;  
            $table->time('from');  
            $table->time('to');  
            
            // Foreign key referencing 'points' table
            $table->unsignedBigInteger('point_id');
            $table->foreign('point_id')->references('id')->on('points');
            
            // Foreign key referencing 'users' table
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->timestamps();  // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_days');
    }
};
