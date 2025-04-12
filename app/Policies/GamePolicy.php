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

    public function view(User $user, Game $game): bool
    {
        return true;
    }
}
