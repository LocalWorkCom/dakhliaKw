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
            $table->enum('type', ['man', 'female'])->default('man');
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->integer('sector')->nullable();   //قطاع 
            $table->string('region')->nullable();  // منطقه
            $table->string('Provinces')->nullable();  // محافظات

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        });
    }
};
