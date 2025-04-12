<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Repositories\GameRepository;

class GameService
{
    private GameRepository $gameRepo;

    public function __construct(GameRepository $gameRepo)
    {
        $this->gameRepo = $gameRepo;
    }

    public function getAll($perPage = 10)
    {
        return $this->gameRepo->paginate($perPage);
    }

    public function create(User $user, array $data): Game
    {
        return $this->gameRepo->create([
            'user_id' => $user->id,
            'name' => $data['name'],
        ]);
    }

    public function update(Game $game, array $data): Game
    {
        $this->gameRepo->update($game, ['name' => $data['name']]);

        return $game;
    }

    public function delete(Game $game): void
    {
        $this->gameRepo->delete($game);
    }
}
