<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Создаёт администратора для страницы статистики.
 *
 * Запуск:
 *   php artisan db:seed --class=AdminSeeder
 *
 * Данные для входа:
 *   Email:    admin@example.com
 *   Password: secret123
 *
 * В продакшне: поменяй пароль через .env или после первого входа.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name'     => 'Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'secret123')),
            ]
        );

        $this->command->info('Admin user created: ' . env('ADMIN_EMAIL', 'admin@example.com'));
    }
}