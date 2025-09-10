<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id('quiz_id');
            $table->jsonb('content');   // أسئلة الكويز
            $table->jsonb('responses')->nullable(); // إجابات الطلاب (ممكن تكون JSON)
            $table->text('feedback')->nullable();   // ملاحظات أو تصحيح
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
