<?php

namespace App\Repositories;

use App\Models\Game;
use App\Models\Mod;

class ModRepository extends BaseRepository
{
    public function __construct(Mod $mod)
    {
        parent::__construct($mod);
    }

    public function create(Game $game, array $data)
    {
        return $game->mods()->create($data);
    }
}
