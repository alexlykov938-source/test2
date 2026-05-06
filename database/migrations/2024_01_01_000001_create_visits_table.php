<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Таблица visits — хранит каждое посещение страницы.
 *
 * ─── Почему SQLite? ──────────────────────────────────────────────────────────
 * Задание допускает SQLite. Для счётчика посещений это оптимально:
 * - Не нужен отдельный сервер БД
 * - Лёгкий деплой (один файл database.sqlite)
 * - SQLite отлично справляется с нагрузкой на чтение для статистики
 * Для high-load продакшна — MySQL/PostgreSQL, но не в рамках задания.
 *
 * ─── Почему не храним полный userAgent в отдельной таблице? ──────────────────
 * Нормализация была бы уместна при миллионах записей.
 * Для задания — денормализованная структура проще и быстрее в разработке.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);                   // IPv4 и IPv6 (до 45 символов)
            $table->string('city')->default('unknown');
            $table->string('country')->default('unknown');
            $table->enum('device', ['mobile', 'tablet', 'desktop'])->default('desktop');
            $table->text('user_agent')->nullable();      // сырой UA для детализации
            $table->string('page')->nullable();          // URL посещённой страницы
            $table->string('referrer')->nullable();      // откуда пришёл
            $table->timestamp('visited_at');             // время визита (от клиента)
            $table->timestamps();                        // created_at — время записи в БД

            // Индексы для запросов статистики
            $table->index('visited_at');                 // запросы по времени (график по часам)
            $table->index('city');                       // группировка по городу (пирог)
            $table->index('ip');                         // поиск уникальных посетителей
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};