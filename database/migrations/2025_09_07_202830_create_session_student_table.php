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
                  ->onDelete('cascade'); 
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            $table->boolean('attended')->default(false); 
           $table->integer('points')->default(0);
           $table->integer('pages_learned')->default(0);

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
