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
        Schema::create('iotelegrams', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['in','out']);
            $table->foreignId('from_departement')->nullable()->references('id')->on('departements')->onDelete('cascade');
            $table->foreignId('representive_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('recieved_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->integer('files_num');

            $table->boolean('active')->default(1);

            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable( )->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iotelegrams');
    }
};
