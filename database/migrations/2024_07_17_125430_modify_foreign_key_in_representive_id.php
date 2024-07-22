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
        Schema::table('iotelegrams', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['representive_id']);

            // Add a new foreign key constraint
            $table->foreign('representive_id')->references('id')->on('postmans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iotelegrams', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['representive_id']);

            // Add back the old foreign key constraint
            $table->foreign('representive_id')->references('id')->on('users');
        });
    }
};
