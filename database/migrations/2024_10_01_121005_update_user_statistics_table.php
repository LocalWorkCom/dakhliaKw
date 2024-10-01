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
        Schema::table('user_statistics', function (Blueprint $table) {
            // Add the `statistic_id` column
            $table->unsignedBigInteger('statistic_id')->nullable()->after('user_id');

            // Add the foreign key constraint for `statistic_id`
            $table->foreign('statistic_id')->references('id')->on('statistics');

            // Add the `checked` boolean column
            $table->boolean('checked')->default(false)->after('statistic_id');

            // Drop the `name` column
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            // Drop the foreign key and the `statistic_id` column
            $table->dropForeign(['statistic_id']);
            $table->dropColumn('statistic_id');

            // Drop the `checked` column
            $table->dropColumn('checked');

            // Re-add the `name` column if rolling back
            $table->string('name')->after('id');
        });
    }
};
