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
            $table->foreignId('from_departement')->unsigned()->references('id')->on('departements')->onDelete('cascade');
            $table->foreignId('from_user')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('recieve_user_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->date('reciept_date')->nullable();
            $table->integer('files_num')->nullable()->default(1);
            $table->boolean('active')->nullable()->default(1);

            $table->foreignId('created_by')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->unsigned()->references('id')->on('users')->onDelete('cascade');

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
