<?php

namespace App\Http\Controllers;

use App\Models\Joke;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JokeController extends Controller
{
    /**
     * GET /api/jokes
     * Список шуток с пагинацией.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 15), 100);
        
        $jokes = Joke::orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $jokes->items(),
            'meta' => [
                'current_page' => $jokes->currentPage(),
                'last_page'    => $jokes->lastPage(),
                'per_page'     => $jokes->perPage(),
                'total'        => $jokes->total(),
            ],
        ]);
    }

    /**
     * GET /api/jokes/{id}
     * Одна шутка по ID.
     */
    public function show(int $id): JsonResponse
    {
        $joke = Joke::findOrFail($id);

        return response()->json([
            'data' => $joke,
        ]);
    }
}