<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для таблицы jokes.
 *
 * Структура повторяет ответ API:
 * {
 *   "id": 1,
 *   "type": "general",
 *   "setup": "Why did the scarecrow win an award?",
 *   "punchline": "Because he was outstanding in his field!"
 * }
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jokes', function (Blueprint $table) {
            $table->id();                                      // PK, auto-increment
            $table->unsignedInteger('api_id')->unique();      // id из API, уникальный — защита от дублей
            $table->string('type');                           // категория: general, knock-knock и т.д.
            $table->text('setup');                            // вопрос/завязка шутки
            $table->text('punchline');                        // ответ/панчлайн
            $table->timestamp('fetched_at')->nullable();      // когда получили от API
            $table->timestamps();                             // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jokes');
    }
};