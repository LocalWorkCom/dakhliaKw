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
        Schema::table('outgoings', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('updated_by'); 
            
            // Set up the foreign key constraint
            $table->foreign('department_id')->references('id')->on('external_departments')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoings', function (Blueprint $table) {
            //
        });
    }
};
