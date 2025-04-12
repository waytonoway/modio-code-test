<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Mod",
 *     required={"id", "game_id", "user_id", "name", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=5),
 *     @OA\Property(property="game_id", type="integer", format="int64", example=5),
 *     @OA\Property(property="name", type="string", example="Mod one"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-02-02T14:45:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-02T15:00:00Z")
 * )
 */
class Mod extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'name',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
