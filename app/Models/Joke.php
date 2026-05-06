<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель Joke.
 *
 * @property int         $id
 * @property int         $api_id
 * @property string      $type
 * @property string      $setup
 * @property string      $punchline
 * @property \Carbon\Carbon|null $fetched_at
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 */
class Joke extends Model
{
    use HasFactory;

    /**
     * Поля, разрешённые для массового заполнения.
     * Перечисляем явно — $guarded = [] считается плохой практикой,
     * т.к. открывает все поля, включая будущие.
     */
    protected $fillable = [
        'api_id',
        'type',
        'setup',
        'punchline',
        'fetched_at',
    ];

    /**
     * Автоматическое приведение типов.
     * fetched_at → Carbon, чтобы работать с датой как с объектом.
     */
    protected $casts = [
        'fetched_at' => 'datetime',
    ];
}