<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Repositories\GameRepository;
use Illuminate\Support\Facades\Cache;

class GameService
{
    private GameRepository $gameRepo;

    public function __construct(GameRepository $gameRepo)
    {
        $this->gameRepo = $gameRepo;
    }

    public function getWithPagination(int $perPage = 10, int $page = 1)
    {
        $cacheKey = 'games_page_' . $perPage . '_' . $page;

        return Cache::rememberForever($cacheKey, function () use ($perPage, $page) {
            return $this->gameRepo->paginate($perPage);
        });
    }

    public function create(User $user, array $data): Game
    {
        $game = $this->gameRepo->create([
            'user_id' => $user->id,
            'name' => $data['name'],
        ]);

        Cache::tags(['games'])->flush();

        return $game;
    }

    public function update(Game $game, array $data): Game
    {
        $this->gameRepo->update($game, ['name' => $data['name']]);

        Cache::tags(['games'])->flush();

        return $game;
    }

    public function delete(Game $game): void
    {
        $this->gameRepo->delete($game);

        Cache::tags(['games'])->flush();
    }
}
