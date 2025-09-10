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
    Schema::create('pages_learned', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الطالب
        $table->integer('page_number'); // رقم الصفحة
        $table->string('sura_name')->nullable(); // اسم السورة (اختياري)
        $table->date('learned_at')->nullable(); // تاريخ الحفظ
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_learneds');
    }
};
