<?php

namespace App\Models;

use App\Traits\WithContextTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id", "name", "email", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Test"),
 *     @OA\Property(property="email", type="string", format="email", example="John@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-11T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-11T10:00:00Z")
 * )
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, WithContextTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (in_array('details', $this->context)) {
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
        }

        return $data;
    }
}
