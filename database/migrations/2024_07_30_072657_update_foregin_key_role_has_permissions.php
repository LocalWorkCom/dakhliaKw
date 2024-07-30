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
        Schema::table('role_has_permissions', function (Blueprint $table) {
            //permission_id
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->nullable()->references('id')->on('roles')->onDelete('restrict')->onUpdate('cascade');

            $table->dropForeign(['permission_id']);
            $table->foreign('permission_id')->nullable()->references('id')->on('permissions')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            //
        });
    }
};
