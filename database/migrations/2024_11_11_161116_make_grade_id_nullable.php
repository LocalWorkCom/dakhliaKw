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
    Schema::table('attendance_employees', function (Blueprint $table) {
        // Drop the existing foreign key if it exists
        $table->dropForeign(['grade_id']);
        // Update grade_id to unsignedBigInteger
        $table->unsignedBigInteger('grade_id')->nullable()->change();
        // Re-add the foreign key constraint
        $table->foreign('grade_id')->references('id')->on('grades');
    });
}
public function down(): void
{
    Schema::table('attendance_employees', function (Blueprint $table) {
        // Drop the foreign key in the down method as well
        $table->dropForeign(['grade_id']);
        // Revert to bigInteger without unsigned in down
        $table->bigInteger('grade_id')->nullable(false)->change();
    });
}
};
