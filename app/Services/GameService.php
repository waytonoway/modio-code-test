<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;

/**
 * GameService
 *
 * @todo Fill this class with business logic relating to games, the service layer is responsible for solving
 *   the problems and producing the result.
 */
class GameService
{
    public function getAll($perPage = 10, $page = 1)
    {
        return Game::query()->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(User $user, array $data): Game
    {
        return Game::create([
            'user_id' => $user->id,
            'name' => $data['name'],
        ]);
    }

    public function update(Game $game, array $data): Game
    {
        $game->update(['name' => $data['name']]);

        return $game;
    }

    public function delete(Game $game): void
    {
        $game->delete();
    }
}
