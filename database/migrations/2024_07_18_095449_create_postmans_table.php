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
        Schema::create('postmans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('national_id')->unique()->nullable();
            $table->string('department_id')->nullable();

            $table->string('phone1')->unique()->nullable();
            $table->string('phone2')->unique()->nullable();
            $table->boolean('active')->default(1);
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable( )->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postmans');
    }
};
