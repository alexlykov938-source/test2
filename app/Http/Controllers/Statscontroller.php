<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        // Все 24 часа с нулями — Chart.js нужна полная ось X без пропусков
        $hourlyRaw  = Visit::uniqueByHour($date)->get()->keyBy('hour');
        $hourlyData = collect(range(0, 23))->map(function ($h) use ($hourlyRaw) {
            $key = str_pad($h, 2, '0', STR_PAD_LEFT);
            return ['hour' => $key . ':00', 'count' => $hourlyRaw->get($key)?->count ?? 0];
        });

        $citiesData  = Visit::byCities(7)->get();
        $totalToday  = Visit::whereDate('visited_at', $date)->count();
        $uniqueToday = Visit::whereDate('visited_at', $date)->distinct('ip')->count('ip');

        return view('stats.index', compact(
            'hourlyData',
            'citiesData',
            'totalToday',
            'uniqueToday',
            'date'
        ));
    }
}