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
        Schema::create('session_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                  ->constrained('learning_sessions')
                  ->onDelete('cascade'); // رابط للجدول الرئيسي للجلسة
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // رابط للطالب
            $table->boolean('attended')->default(false); // لتسجيل حضور الطالب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_student');
    }
};
