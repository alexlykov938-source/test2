<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Каждые 5 минут: получаем шутку из API и сохраняем в БД
Schedule::command('jokes:fetch')
    ->everyFiveMinutes()
    ->withoutOverlapping()   // не запускать повторно, если предыдущий ещё не завершился
    ->onFailure(function () {
        // При необходимости — отправить уведомление, записать в лог и т.д.
        \Illuminate\Support\Facades\Log::error('Scheduled jokes:fetch failed');
    });