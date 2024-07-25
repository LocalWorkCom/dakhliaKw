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
            // Drop the existing foreign key constraint
            $table->dropForeign(['person_to']);

            // Add the new foreign key constraint
            $table->foreign('person_to')->references('id')->on('export_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoings', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['person_to']);

            // Add the old foreign key constraint back
            $table->foreign('person_to')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
