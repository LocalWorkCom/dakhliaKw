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
            $table->string('seniority')->nullable();   //الاقدمية
            $table->string('public_administration')->nullable();  //الادارة العامة
            $table->string('work_location')->nullable();  //مقر العمل
            $table->integer('position')->nullable();   //المنصب
            $table->string('qualification')->nullable();  //المؤهل 
            $table->string('date_of_birth')->nullable();   //تاريخ الميلاد 
            $table->string('joining_date')->nullable();   //تاريخ الالتحاق
            $table->string('age')->nullable();   //العمر
            $table->string('length_of_service')->nullable();  //مدة الخدمة 
            $table->string('image')->nullable();  //صورة شخصيه
            $table->foreignId('grade_id')->nullable()->references('id')->on('grades')->onDelete('cascade');  //الرتبه
            $table->foreignId('department_id')->nullable()->references('id')->on('departements')->onDelete('cascade');  // القسم
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
