<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'ip',
        'city',
        'country',
        'device',
        'user_agent',
        'page',
        'referrer',
        'visited_at',
    ];

    /**
     * Scope: Уникальные посещения по часам за конкретную дату.
     * «Уникальный» = COUNT(DISTINCT ip) — один IP в пределах часа = один посетитель.
     *
     * Альтернативы и почему они не выбраны:
     * - Сессионный токен (куки) — точнее, но требует GDPR-согласия и не работает при первом визите.
     * - Fingerprint (Canvas/WebGL) — ещё точнее, но медленнее и сложнее, нарушает приватность.
     *   IP — компромисс точности и простоты для демо-проекта.
     */
    public function scopeUniqueByHour($query, string $date)
    {
        return $query
            ->whereDate('visited_at', $date)
            ->selectRaw('strftime("%H", visited_at) as hour, COUNT(DISTINCT ip) as count')
            ->groupByRaw('strftime("%H", visited_at)')
            ->orderBy('hour');
    }

    /**
     * Scope: Статистика по городам за последние N дней.
     * limit(10) — чтобы пироговая диаграмма оставалась читаемой.
     */
    public function scopeByCities($query, int $days = 7)
    {
        return $query
            ->where('visited_at', '>=', now()->subDays($days))
            ->selectRaw('city, COUNT(DISTINCT ip) as count')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10);
    }
}