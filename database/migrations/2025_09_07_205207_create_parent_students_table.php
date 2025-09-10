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
    Schema::create('parent_student', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parent_id')->constrained('users')->onDelete('cascade'); // ولي الأمر
        $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // الطالب
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_students');
    }
};
