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
            $table->string('job')->nullable()->change();
            $table->string('job_title')->nullable()->change();
            $table->string('nationality')->nullable()->change();
            $table->string('Civil_number')->nullable()->change();
            $table->string('file_number')->nullable()->change();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In a down migration, you would typically reverse changes made in the up method.
        // Since your up method adds a foreign key constraint, the down method should remove it.

    }
};
