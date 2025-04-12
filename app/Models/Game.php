<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Game",
 *     required={"id", "user_id", "name", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=5),
 *     @OA\Property(property="name", type="string", example="Chess Tournament"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-02-02T14:45:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-02T15:00:00Z")
 * )
 */
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mods()
    {
        return $this->hasMany(Mod::class);
    }
}
