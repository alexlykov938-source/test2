<?php

namespace App\Console\Commands;

use App\Models\Joke;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Консольная команда: получает шутку из API и сохраняет в БД.
 *
 * Запуск вручную:
 *   php artisan jokes:fetch
 *
 * Запуск по расписанию (каждые 5 минут) — настроен в routes/console.php.
 *
 * ─── Почему именно этот API? ─────────────────────────────────────────────────
 * https://official-joke-api.appspot.com/random_joke
 *  + Бесплатный, без регистрации и токена
 *  + Простая JSON-структура (4 поля)
 *  + Стабильный, публично известный эндпоинт
 *  - Нет пагинации, но для демо это несущественно
 *
 * ─── Почему updateOrCreate, а не просто create? ───────────────────────────────
 * API может вернуть одну и ту же шутку повторно (случайная выборка).
 * updateOrCreate по api_id:
 *  - не дублирует записи
 *  - обновляет fetched_at, если шутка пришла снова (полезно для аудита)
 * Альтернатива — firstOrCreate — не обновляла бы fetched_at.
 */
class FetchJokes extends Command
{
    /**
     * Сигнатура команды.
     * jokes:fetch — namespace:action, стандартное соглашение Laravel.
     */
    protected $signature = 'jokes:fetch';

    protected $description = 'Fetch a random joke from API and store it in the database';

    /**
     * Точка входа команды.
     */
    public function handle(): int
    {
        $this->info('Fetching joke from API...');

        try {
            // 1. HTTP-запрос к API
            // Http::timeout(10) — не ждём вечно, если API лагает
            $response = Http::timeout(10)->get('https://official-joke-api.appspot.com/random_joke');

            // 2. Проверяем HTTP-статус
            if (!$response->successful()) {
                $this->error("API responded with status: {$response->status()}");
                Log::error('FetchJokes: API error', ['status' => $response->status()]);

                return Command::FAILURE;
            }

            // 3. Декодируем JSON
            $data = $response->json();

            // 4. Базовая валидация структуры ответа
            // Если API изменит формат — узнаем сразу, а не при чтении из БД
            if (!isset($data['id'], $data['type'], $data['setup'], $data['punchline'])) {
                $this->error('Unexpected API response structure.');
                Log::error('FetchJokes: unexpected structure', ['data' => $data]);

                return Command::FAILURE;
            }

            // 5. Сохранение в БД
            // updateOrCreate([условие поиска], [поля для создания/обновления])
            $joke = Joke::updateOrCreate(
                ['api_id' => $data['id']],
                [
                    'type'       => $data['type'],
                    'setup'      => $data['setup'],
                    'punchline'  => $data['punchline'],
                    'fetched_at' => now(),
                ]
            );

            // 6. Информируем о результате
            $action = $joke->wasRecentlyCreated ? 'Created' : 'Updated';
            $this->info("{$action} joke #{$joke->api_id}: {$joke->setup}");

            Log::info("FetchJokes: {$action} joke", ['api_id' => $joke->api_id]);

            return Command::SUCCESS;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Отдельно ловим сетевые ошибки — timeout, DNS и т.д.
            $this->error("Connection error: {$e->getMessage()}");
            Log::error('FetchJokes: connection error', ['message' => $e->getMessage()]);

            return Command::FAILURE;

        } catch (\Throwable $e) {
            // Всё остальное — непредвиденные ошибки
            $this->error("Unexpected error: {$e->getMessage()}");
            Log::error('FetchJokes: unexpected error', ['message' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}