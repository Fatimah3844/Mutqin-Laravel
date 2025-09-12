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
        Schema::create('learning_sessions', function (Blueprint $table) {
             $table->id();
        $table->foreignId('sheikh_id')->constrained('users')->onDelete('cascade');
        $table->string('calendly_id')->nullable();
        $table->dateTime('start_time');
        $table->dateTime('end_time')->nullable();
        $table->enum('status', ['upcoming', 'done', 'cancelled'])->default('upcoming');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_sessions');
    }
};
