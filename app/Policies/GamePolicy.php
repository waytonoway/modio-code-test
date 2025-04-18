<?php
namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    public function manage(User $user, Game $game): bool
    {
        return $user->id === $game->user_id;
    }

    public function create(User $user): bool
    {
        return !!$user->id;
    }

    public function view(?User $user): bool
    {
        return true;
    }
}
