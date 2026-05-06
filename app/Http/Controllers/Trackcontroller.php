<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TrackController — принимает POST-запросы от tracker.js
 * Эндпоинт: POST /api/track
 *
 * Почему IP берём из $request->ip(), а не из тела запроса:
 * - $request->ip() — серверная переменная, невозможно подделать
 * - Данные от клиента — можно подменить (curl с кастомным body)
 *   Это защита от спама фейковыми IP.
 */
class TrackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city'     => ['sometimes', 'string', 'max:100'],
            'device'   => ['required', 'string', 'in:mobile,tablet,desktop'],
            'page'     => ['sometimes', 'string', 'max:2048'],
            'referrer' => ['nullable', 'string', 'max:2048'],
        ]);

        // Серверные данные — единственный верный источник
        $validated['ip']         = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['visited_at'] = now();

        Visit::create($validated);

        // 204 No Content — стандарт REST для операции без возвращаемых данных
        return response()->json(null, 204);
    }
}