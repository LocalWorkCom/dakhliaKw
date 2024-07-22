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
        Schema::table('postmans', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In a down migration, you would typically reverse changes made in the up method.
        // Since your up method adds a foreign key constraint, the down method should remove it.

        Schema::table('postmans', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};
