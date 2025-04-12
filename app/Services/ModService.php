<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use App\Repositories\ModRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ModService
{
    private ModRepository $modRepo;

    public function __construct(ModRepository $modRepo)
    {
        $this->modRepo = $modRepo;
    }
    public function create(User $user, Game $game, array $data): Mod
    {
        return $this->modRepo->create($game, [
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(Mod $mod, array $data): Mod
    {
        $this->modRepo->update($mod, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return $mod;
    }

    public function delete(Mod $mod): void
    {
        $this->modRepo->delete($mod);
    }

    public function getModsForGame(Game $game, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $game->mods()
            ->paginate($perPage, ['*'], 'page', $page);
    }
}

