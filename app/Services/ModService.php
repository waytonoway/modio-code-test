<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use App\Repositories\ModRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ModService
{
    private ModRepository $modRepo;

    public function __construct(ModRepository $modRepo)
    {
        $this->modRepo = $modRepo;
    }
    public function create(User $user, Game $game, array $data): Mod
    {
        $mod = $this->modRepo->create($game, [
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        Cache::tags(['mods', 'game_' . $game->id])->flush();

        return $mod;
    }

    public function update(Mod $mod, array $data): Mod
    {
        $this->modRepo->update($mod, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        Cache::tags(['mods', 'game_' . $mod->game_id])->flush();

        return $mod;
    }

    public function delete(Mod $mod): void
    {
        $this->modRepo->delete($mod);

        Cache::tags(['mods', 'game_' . $mod->game_id])->flush();
    }

    public function getModsForGame(Game $game, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = "mods_for_game_{$game->id}_{$perPage}_{$page}";

        return Cache::rememberForever($cacheKey, function () use ($game, $perPage, $page) {
            return $game->mods()
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }
}

